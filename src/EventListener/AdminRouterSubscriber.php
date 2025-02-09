<?php

namespace EasyCorp\Bundle\EasyAdminBundle\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Config\Cache;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Router\AdminRouteGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\ControllerFactory;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * This subscriber acts as a "proxy" of all backend requests. First, if the
 * request is related to EasyAdmin, it creates the AdminContext variable and
 * stores it in the Request as an attribute.
 *
 * Second, it uses Symfony events to serve all backend requests using a single
 * route. The trick is to change dynamically the controller to execute when
 * the request is related to a CRUD action or a normal Symfony route/action.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class AdminRouterSubscriber implements EventSubscriberInterface
{
    private AdminContextFactory $adminContextFactory;
    private ControllerFactory $controllerFactory;
    private ControllerResolverInterface $controllerResolver;
    private UrlGeneratorInterface $urlGenerator;
    private RequestMatcherInterface $requestMatcher;
    private CacheItemPoolInterface $cache;
    private AdminRouteGeneratorInterface $adminRouteGenerator;

    public function __construct(AdminContextFactory $adminContextFactory, ControllerFactory $controllerFactory, ControllerResolverInterface $controllerResolver, UrlGeneratorInterface $urlGenerator, RequestMatcherInterface $requestMatcher, CacheItemPoolInterface $cache, AdminRouteGenerator $adminRouteGenerator)
    {
        $this->adminContextFactory = $adminContextFactory;
        $this->controllerFactory = $controllerFactory;
        $this->controllerResolver = $controllerResolver;
        $this->urlGenerator = $urlGenerator;
        $this->requestMatcher = $requestMatcher;
        $this->cache = $cache;
        $this->adminRouteGenerator = $adminRouteGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['onKernelRequest', 1],
            ],
            // the priority must be higher than 0 to run it before ParamConverterListener
            ControllerEvent::class => ['onKernelController', 128],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (false === $request->attributes->getBoolean(EA::ROUTE_CREATED_BY_EASYADMIN)) {
            return;
        }

        // edge-case: in some scenarios, admin routes are generated by the custom route loader
        // and their information is cached but then removed from the cache (e.g. when running
        // 'rm -fr var/cache/* && bin/console cache:clear'). If that's the case, regenerate the
        // admin routes to force saving them in the cache again.
        // see https://github.com/EasyCorp/EasyAdminBundle/issues/6680
        $adminRoutes = $this->cache->getItem(Cache::ROUTE_NAME_TO_ATTRIBUTES)->get();
        if (null === $adminRoutes) {
            $this->adminRouteGenerator->generateAll();
        }

        $dashboardControllerFqcn = $request->attributes->get(EA::DASHBOARD_CONTROLLER_FQCN);
        if (null === $dashboardControllerInstance = $this->getDashboardControllerInstance($dashboardControllerFqcn, $request)) {
            return;
        }

        // creating the context is expensive, so it's created once and stored in the request
        // if the current request already has an AdminContext object, do nothing
        if (null === $adminContext = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            $crudControllerFqcn = $request->attributes->get(EA::CRUD_CONTROLLER_FQCN);
            $actionName = $request->attributes->get(EA::CRUD_ACTION);

            $crudControllerInstance = $this->controllerFactory->getCrudControllerInstance($crudControllerFqcn, $actionName, $request);
            $adminContext = $this->adminContextFactory->create($request, $dashboardControllerInstance, $crudControllerInstance, $actionName);
        }

        $request->attributes->set(EA::CONTEXT_REQUEST_ATTRIBUTE, $adminContext);
    }

    /**
     * In EasyAdmin all backend requests are served via the same route (that allows to
     * detect under which dashboard you want to process the request). This method handles
     * the requests related to "CRUD controller actions" and "custom Symfony actions".
     * The trick used is to change dynamically the controller executed by Symfony.
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        if (null === $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            return;
        }

        // if the request is related to a custom action, change the controller to be executed
        if (null !== $request->query->get(EA::ROUTE_NAME)) {
            $symfonyControllerAsString = $this->getSymfonyControllerFqcn($request);
            $symfonyControllerCallable = $this->getSymfonyControllerInstance($symfonyControllerAsString, $request->query->all()[EA::ROUTE_PARAMS] ?? []);
            if (false !== $symfonyControllerCallable) {
                // this makes Symfony believe that another controller is being executed
                // (e.g. this is needed for the autowiring of controller action arguments)
                // VERY IMPORTANT: here the Symfony controller must be passed as a string ('App\Controller\Foo::index')
                // Otherwise, the param converter of the controller method doesn't work
                $event->getRequest()->attributes->set('_controller', $symfonyControllerAsString);
                // route params must be added as route attribute; otherwise, param converters don't work
                $event->getRequest()->attributes->replace(array_merge(
                    $request->query->all()[EA::ROUTE_PARAMS] ?? [],
                    $event->getRequest()->attributes->all()
                ));

                // this actually makes Symfony to execute the other controller
                $event->setController($symfonyControllerCallable);
            }
        }
    }

    private function getDashboardControllerInstance(string $dashboardControllerFqcn, Request $request): ?DashboardControllerInterface
    {
        return $this->controllerFactory->getDashboardControllerInstance($dashboardControllerFqcn, $request);
    }

    private function getSymfonyControllerFqcn(Request $request): ?string
    {
        $routeName = $request->query->get(EA::ROUTE_NAME);
        $routeParams = $request->query->all()[EA::ROUTE_PARAMS] ?? [];
        $url = $this->urlGenerator->generate($routeName, $routeParams);

        $newRequest = $request->duplicate();
        $newRequest->attributes->remove('_controller');
        $newRequest->attributes->set('_route', $routeName);
        $newRequest->attributes->add($routeParams);
        $newRequest->server->set('REQUEST_URI', $url);

        $parameters = $this->requestMatcher->matchRequest($newRequest);

        return $parameters['_controller'] ?? null;
    }

    private function getSymfonyControllerInstance(string $controllerFqcn, array $routeParams): callable|false
    {
        $newRequest = new Request([], [], ['_controller' => $controllerFqcn, '_route_params' => $routeParams], [], [], []);

        return $this->controllerResolver->getController($newRequest);
    }
}

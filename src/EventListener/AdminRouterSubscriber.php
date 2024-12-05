<?php

namespace EasyCorp\Bundle\EasyAdminBundle\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
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

    public function __construct(AdminContextFactory $adminContextFactory, ControllerFactory $controllerFactory, ControllerResolverInterface $controllerResolver, UrlGeneratorInterface $urlGenerator, RequestMatcherInterface $requestMatcher, CacheItemPoolInterface $cache)
    {
        $this->adminContextFactory = $adminContextFactory;
        $this->controllerFactory = $controllerFactory;
        $this->controllerResolver = $controllerResolver;
        $this->urlGenerator = $urlGenerator;
        $this->requestMatcher = $requestMatcher;
        $this->cache = $cache;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['onKernelRequestPrettyUrls', 1],
                ['onKernelRequest', 0],
            ],
            // the priority must be higher than 0 to run it before ParamConverterListener
            ControllerEvent::class => ['onKernelController', 128],
        ];
    }

    public function onKernelRequestPrettyUrls(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if (null === $routeName) {
            return;
        }

        $adminRoutes = $this->cache->getItem(AdminRouteGenerator::CACHE_KEY_ROUTE_TO_FQCN)->get();
        if (null === $adminRoutes || !\array_key_exists($routeName, $adminRoutes)) {
            return;
        }

        $request->attributes->set(EA::ROUTE_CREATED_BY_EASYADMIN, true);

        $dashboardControllerFqcn = $adminRoutes[$routeName][EA::DASHBOARD_CONTROLLER_FQCN];
        if (null === $dashboardControllerInstance = $this->getDashboardControllerInstance($dashboardControllerFqcn, $request)) {
            return;
        }

        // creating the context is expensive, so it's created once and stored in the request
        // if the current request already has an AdminContext object, do nothing
        if (null === $adminContext = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            $crudControllerFqcn = $adminRoutes[$routeName][EA::CRUD_CONTROLLER_FQCN];
            $actionName = $adminRoutes[$routeName][EA::CRUD_ACTION];

            $request->attributes->set(EA::DASHBOARD_CONTROLLER_FQCN, $dashboardControllerFqcn);
            $request->attributes->set(EA::CRUD_CONTROLLER_FQCN, $crudControllerFqcn);
            $request->attributes->set(EA::CRUD_ACTION, $actionName);

            $crudControllerInstance = $this->controllerFactory->getCrudControllerInstance($crudControllerFqcn, $actionName, $request);
            $adminContext = $this->adminContextFactory->create($request, $dashboardControllerInstance, $crudControllerInstance, $actionName);
        }

        $request->attributes->set(EA::CONTEXT_REQUEST_ATTRIBUTE, $adminContext);
    }

    /**
     * If this is an EasyAdmin request, it creates the AdminContext variable, stores it
     * in the Request as an attribute and injects it as a global Twig variable.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (null === $dashboardControllerFqcn = $this->getDashboardControllerFqcn($request)) {
            return;
        }

        if (null === $dashboardControllerInstance = $this->getDashboardControllerInstance($dashboardControllerFqcn, $request)) {
            return;
        }

        // creating the context is expensive, so it's created once and stored in the request
        // if the current request already has an AdminContext object, do nothing
        if (null === $adminContext = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            $crudControllerInstance = $this->getCrudControllerInstance($request);
            $adminContext = $this->adminContextFactory->create($request, $dashboardControllerInstance, $crudControllerInstance);
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

        // if the request is related to a CRUD controller, change the controller to be executed
        if (null !== $crudControllerInstance = $this->getCrudControllerInstance($request)) {
            $symfonyControllerFqcnCallable = [$crudControllerInstance, $request->attributes->get(EA::CRUD_ACTION) ?? $request->query->get(EA::CRUD_ACTION)];
            $symfonyControllerStringCallable = [$crudControllerInstance::class, $request->attributes->get(EA::CRUD_ACTION) ?? $request->query->get(EA::CRUD_ACTION)];

            // this makes Symfony believe that another controller is being executed
            // (e.g. this is needed for the autowiring of controller action arguments)
            // VERY IMPORTANT: here the Symfony controller must be passed as a string (['App\Controller\Foo', 'index'])
            // Otherwise, the param converter of the controller method doesn't work
            $event->getRequest()->attributes->set('_controller', $symfonyControllerStringCallable);

            // this actually makes Symfony to execute the other controller
            $event->setController($symfonyControllerFqcnCallable);
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

    /**
     * It returns the FQCN of the EasyAdmin Dashboard controller used to serve this
     * request or null if this is not an EasyAdmin request.
     * Because of how EasyAdmin works, all backend requests are handled via the
     * Dashboard controller, so its enough to check if the request controller implements
     * the DashboardControllerInterface.
     */
    private function getDashboardControllerFqcn(Request $request): ?string
    {
        $controller = $request->attributes->get('_controller');
        $controllerFqcn = null;

        if (\is_string($controller)) {
            [$controllerFqcn, ] = explode('::', $controller);
        }

        if (\is_array($controller)) {
            $controllerFqcn = $controller[0];
        }

        if (\is_object($controller)) {
            $controllerFqcn = $controller::class;
        }

        return is_subclass_of($controllerFqcn, DashboardControllerInterface::class) ? $controllerFqcn : null;
    }

    private function getDashboardControllerInstance(string $dashboardControllerFqcn, Request $request): ?DashboardControllerInterface
    {
        return $this->controllerFactory->getDashboardControllerInstance($dashboardControllerFqcn, $request);
    }

    private function getCrudControllerInstance(Request $request): ?CrudControllerInterface
    {
        // when using pretty URLs, the data is in the request attributes instead of the query string
        $crudControllerFqcn = $request->attributes->get(EA::CRUD_CONTROLLER_FQCN) ?? $request->query->get(EA::CRUD_CONTROLLER_FQCN);
        $crudAction = $request->attributes->get(EA::CRUD_ACTION) ?? $request->query->get(EA::CRUD_ACTION);

        return $this->controllerFactory->getCrudControllerInstance($crudControllerFqcn, $crudAction, $request);
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

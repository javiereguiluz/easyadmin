<?php

namespace EasyCorp\Bundle\EasyAdminBundle\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Cache\CacheWarmer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Router\AdminRouteGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\ControllerFactory;
use EasyCorp\Bundle\EasyAdminBundle\Registry\CrudControllerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    private CrudControllerRegistry $crudControllerRegistry;
    private string $buildDir;
    private bool $requestAlreadyProcessedAsPrettyUrl = false;

    public function __construct(AdminContextFactory $adminContextFactory, ControllerFactory $controllerFactory, ControllerResolverInterface $controllerResolver, UrlGeneratorInterface $urlGenerator, RequestMatcherInterface $requestMatcher, CacheItemPoolInterface $cache, AdminRouteGenerator $adminRouteGenerator, string $buildDir, CrudControllerRegistry $crudControllerRegistry)
    {
        $this->adminContextFactory = $adminContextFactory;
        $this->controllerFactory = $controllerFactory;
        $this->controllerResolver = $controllerResolver;
        $this->urlGenerator = $urlGenerator;
        $this->requestMatcher = $requestMatcher;
        $this->cache = $cache;
        $this->adminRouteGenerator = $adminRouteGenerator;
        $this->buildDir = $buildDir;
        $this->crudControllerRegistry = $crudControllerRegistry;
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
        if (false === $request->attributes->getBoolean(EA::ROUTE_CREATED_BY_EASYADMIN)) {
            // at this point, the incoming request can be:
            //   (1) A dashboard URL (they don't have the EasyAdmin route attributes) of an app using pretty URLs
            //   (2) An ugly URL from EasyAdmin (they all use the main dashboard URL and select the action to execute via query params)
            //   (3) a regular Symfony request (not related to EasyAdmin)

            // check if the URL includes the 'crudControllerFqcn' query parameter
            // if it does, this is case (2) and we don't handle it as a pretty URL
            if ($request->query->has(EA::CRUD_CONTROLLER_FQCN)) {
                return;
            }

            // this can be case (1) or (3). Sadly, the dashboard routes don't include
            // the EasyAdmin route attributes because they are regular Symfony routes.
            // I can't find any way (inside our custom route loader, in a compiler pass, etc.) to add the
            // custom EasyAdmin route defaults/attributes to other existing Symfony routes. So, we have to
            // check if the route of the current request matches any of the cached dashboard routes
            $dashboardRoutesCachePath = $this->buildDir.'/'.CacheWarmer::DASHBOARD_ROUTES_CACHE;
            $dashboardControllerRoutes = [];
            if (file_exists($dashboardRoutesCachePath)) {
                try {
                    $dashboardControllerRoutes = require $dashboardRoutesCachePath;
                } catch (\Throwable) {
                }
            }

            // this is not a cached dashboard route, so this is case (3) a regular Symfony request
            if (!\array_key_exists($request->attributes->get('_route'), $dashboardControllerRoutes)) {
                return;
            }
        }

        // edge-case: in some scenarios, admin routes are generated by the custom route loader
        // and their information is cached but then removed from the cache (e.g. when running
        // 'rm -fr var/cache/* && bin/console cache:clear'). If that's the case, regenerate the
        // admin routes (only if the app uses pretty URLs) to force saving them in the cache again.
        // see https://github.com/EasyCorp/EasyAdminBundle/issues/6680
        $adminRoutes = $this->cache->getItem(AdminRouteGenerator::CACHE_KEY_ROUTE_TO_FQCN)->get();
        if (null === $adminRoutes && $this->adminRouteGenerator->usesPrettyUrls()) {
            $this->adminRouteGenerator->generateAll();
        }

        $dashboardControllerFqcn = $request->attributes->get(EA::DASHBOARD_CONTROLLER_FQCN);
        if (null === $dashboardControllerFqcn) {
            if (!isset($dashboardControllerRoutes[$request->attributes->get('_route')])) {
                return;
            }

            $dashboardCallableAsString = $dashboardControllerRoutes[$request->attributes->get('_route')];
            [$dashboardControllerFqcn, ] = explode('::', $dashboardCallableAsString);
        }

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
        $this->requestAlreadyProcessedAsPrettyUrl = true;
    }

    /**
     * If this is an EasyAdmin request, it creates the AdminContext variable, stores it
     * in the Request as an attribute and injects it as a global Twig variable.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->requestAlreadyProcessedAsPrettyUrl) {
            return;
        }

        // return early if this is not a URL associated with EasyAdmin
        $request = $event->getRequest();
        if (null === $dashboardControllerFqcn = $this->getDashboardControllerFqcn($request)) {
            return;
        }

        // if this is a ugly URL from legacy EasyAdmin versions and the application
        // uses pretty URLs, redirect to the equivalent pretty URL
        if ($this->adminRouteGenerator->usesPrettyUrls() && null !== $entityFqcnOrCrudControllerFqcn = $request->query->get(EA::CRUD_CONTROLLER_FQCN)) {
            if (is_subclass_of($entityFqcnOrCrudControllerFqcn, CrudControllerInterface::class)) {
                $crudControllerFqcn = $entityFqcnOrCrudControllerFqcn;
            } else {
                $crudControllerFqcn = $this->crudControllerRegistry->findCrudFqcnByEntityFqcn($entityFqcnOrCrudControllerFqcn);
            }

            $prettyUrlRoute = $this->adminRouteGenerator->findRouteName($dashboardControllerFqcn, $crudControllerFqcn, $request->query->get(EA::CRUD_ACTION, ''));
            if (null !== $prettyUrlRoute) {
                $request->query->remove(EA::CRUD_CONTROLLER_FQCN);

                $event->setResponse(new RedirectResponse($this->urlGenerator->generate($prettyUrlRoute, $request->query->all())));

                return;
            }
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

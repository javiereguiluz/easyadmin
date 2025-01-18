<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Router;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Router\AdminRouteGeneratorInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class AdminRouteGenerator implements AdminRouteGeneratorInterface
{
    public const CACHE_KEY_ROUTE_TO_FQCN = 'easyadmin.routes.route_to_fqcn';
    public const CACHE_KEY_FQCN_TO_ROUTE = 'easyadmin.routes.fqcn_to_route';

    private const DEFAULT_ROUTES_CONFIG = [
        'index' => [
            'routePath' => '/',
            'routeName' => 'index',
            'methods' => ['GET'],
        ],
        'new' => [
            'routePath' => '/new',
            'routeName' => 'new',
            'methods' => ['GET', 'POST'],
        ],
        'batchDelete' => [
            'routePath' => '/batch-delete',
            'routeName' => 'batch_delete',
            'methods' => ['POST'],
        ],
        'autocomplete' => [
            'routePath' => '/autocomplete',
            'routeName' => 'autocomplete',
            'methods' => ['GET'],
        ],
        'renderFilters' => [
            'routePath' => '/render-filters',
            'routeName' => 'render_filters',
            'methods' => ['GET'],
        ],
        'edit' => [
            'routePath' => '/{entityId}/edit',
            'routeName' => 'edit',
            'methods' => ['GET', 'POST', 'PATCH'],
        ],
        'delete' => [
            'routePath' => '/{entityId}/delete',
            'routeName' => 'delete',
            'methods' => ['POST'],
        ],
        'detail' => [
            'routePath' => '/{entityId}',
            'routeName' => 'detail',
            'methods' => ['GET'],
        ],
    ];

    private ?bool $applicationUsesPrettyUrls = null;

    public function __construct(
        private iterable $dashboardControllers,
        private iterable $crudControllers,
        private CacheItemPoolInterface $cache,
        private Filesystem $filesystem,
        private string $buildDir,
    ) {
    }

    public function generateAll(): RouteCollection
    {
        $collection = new RouteCollection();
        $adminRoutes = $this->generateAdminRoutes();

        foreach ($adminRoutes as $routeName => $route) {
            $collection->add($routeName, $route);
        }

        // this dumps all admin routes in a performance-optimized format to later
        // find them quickly without having to use Symfony's router service
        $this->saveAdminRoutesInCache($adminRoutes);

        return $collection;
    }

    // Temporary utility method to be removed in EasyAdmin 5, when the pretty URLs will be mandatory
    // TODO: remove this method in EasyAdmin 5.x
    public function usesPrettyUrls(): bool
    {
        return $this->applicationUsesPrettyUrls ??= $this->filesystem->exists(sprintf('%s/%s', $this->buildDir, AdminRouteLoader::PRETTY_URLS_CONTEXT_FILE_NAME));
    }

    public function findRouteName(?string $dashboardFqcn = null, ?string $crudControllerFqcn = null, ?string $actionName = null): ?string
    {
        $adminRoutes = $this->cache->getItem(self::CACHE_KEY_FQCN_TO_ROUTE)->get();

        if (null === $dashboardFqcn) {
            $dashboardControllers = iterator_to_array($this->dashboardControllers);
            $dashboardFqcn = $dashboardControllers[array_key_first($dashboardControllers)]::class;
        }

        return $adminRoutes[$dashboardFqcn][$crudControllerFqcn ?? ''][$actionName ?? ''] ?? null;
    }

    /**
     * @return array<string, Route>
     */
    private function generateAdminRoutes(): array
    {
        /** @var array<string, Route> $adminRoutes Stores the collection of admin routes created for the app */
        $adminRoutes = [];
        /** @var array<string> $addedRouteNames Temporary cache that stores the route names to ensure that we don't add duplicated admin routes */
        $addedRouteNames = [];

        foreach ($this->dashboardControllers as $dashboardController) {
            $dashboardFqcn = $dashboardController::class;
            [$allowedCrudControllers, $deniedCrudControllers] = $this->getAllowedAndDeniedControllers($dashboardFqcn);
            $defaultRoutesConfig = $this->getDefaultRoutesConfig($dashboardFqcn);
            $dashboardRouteConfig = $this->getDashboardsRouteConfig()[$dashboardFqcn];

            foreach ($this->crudControllers as $crudController) {
                $crudControllerFqcn = $crudController::class;

                if (null !== $allowedCrudControllers && !\in_array($crudControllerFqcn, $allowedCrudControllers, true)) {
                    continue;
                }

                if (null !== $deniedCrudControllers && \in_array($crudControllerFqcn, $deniedCrudControllers, true)) {
                    continue;
                }

                $crudControllerRouteConfig = $this->getCrudControllerRouteConfig($crudControllerFqcn);
                $actionsRouteConfig = array_replace_recursive($defaultRoutesConfig, $this->getCustomActionsConfig($crudControllerFqcn));
                // by default, the 'detail' route uses a catch-all route pattern (/{entityId});
                // so, if the user hasn't customized the 'detail' route path, we need to sort the actions
                // to make sure that the 'detail' action is always the last one
                if ('/{entityId}' === $actionsRouteConfig['detail']['routePath']) {
                    uasort($actionsRouteConfig, static function ($a, $b) {
                        return match (true) {
                            'detail' === $a['routeName'] => 1,
                            'detail' === $b['routeName'] => -1,
                            default => 0,
                        };
                    });
                }

                foreach (array_keys($actionsRouteConfig) as $actionName) {
                    $actionRouteConfig = $actionsRouteConfig[$actionName];
                    $adminRoutePath = rtrim(sprintf('%s/%s/%s', $dashboardRouteConfig['routePath'], $crudControllerRouteConfig['routePath'], ltrim($actionRouteConfig['routePath'], '/')), '/');
                    $adminRouteName = sprintf('%s_%s_%s', $dashboardRouteConfig['routeName'], $crudControllerRouteConfig['routeName'], $actionRouteConfig['routeName']);

                    if (\in_array($adminRouteName, $addedRouteNames, true)) {
                        throw new \RuntimeException(sprintf('When using pretty URLs, all CRUD controllers must have unique PHP class names to generate unique route names. However, your application has at least two controllers with the FQCN "%s", generating the route "%s". Even if both CRUD controllers are in different namespaces, they cannot have the same class name. Rename one of these controllers to resolve the issue.', $crudControllerFqcn, $adminRouteName));
                    }

                    $defaults = [
                        '_controller' => $crudControllerFqcn.'::'.$actionName,
                        EA::ROUTE_CREATED_BY_EASYADMIN => true,
                        EA::DASHBOARD_CONTROLLER_FQCN => $dashboardFqcn,
                        EA::CRUD_CONTROLLER_FQCN => $crudControllerFqcn,
                        EA::CRUD_ACTION => $actionName,
                    ];

                    $adminRoute = new Route($adminRoutePath, defaults: $defaults, methods: $actionRouteConfig['methods']);
                    $adminRoutes[$adminRouteName] = $adminRoute;
                    $addedRouteNames[] = $adminRouteName;
                }
            }
        }

        return $adminRoutes;
    }

    /**
     * @return array{0: class-string[]|null, 1: class-string[]|null}
     */
    private function getAllowedAndDeniedControllers(string $dashboardFqcn): array
    {
        if (null === $attribute = $this->getPhpAttributeInstance($dashboardFqcn, AdminDashboard::class)) {
            return [null, null];
        }

        if (null !== $attribute->allowedControllers && null !== $attribute->deniedControllers) {
            throw new \RuntimeException(sprintf('In the #[AdminDashboard] attribute of the "%s" dashboard controller, you cannot define both "allowedControllers" and "deniedControllers" at the same time because they are the exact opposite. Use only one of them.', $dashboardFqcn));
        }

        return [$attribute->allowedControllers, $attribute->deniedControllers];
    }

    private function getDefaultRoutesConfig(string $dashboardFqcn): array
    {
        if (null === $dashboardAttribute = $this->getPhpAttributeInstance($dashboardFqcn, AdminDashboard::class)) {
            return self::DEFAULT_ROUTES_CONFIG;
        }

        if (null === $customRoutesConfig = $dashboardAttribute->routes) {
            return self::DEFAULT_ROUTES_CONFIG;
        }

        foreach ($customRoutesConfig as $action => $customRouteConfig) {
            if (\count(array_diff(array_keys($customRouteConfig), ['routePath', 'routeName'])) > 0) {
                throw new \RuntimeException(sprintf('In the #[AdminDashboard] attribute of the "%s" dashboard controller, the route configuration for the "%s" action defines some unsupported keys. You can only define these keys: "routePath" and "routeName".', $dashboardFqcn, $action));
            }

            if (isset($customRouteConfig['routeName']) && !preg_match('/^[a-zA-Z0-9_-]+$/', $customRouteConfig['routeName'])) {
                throw new \RuntimeException(sprintf('In the #[AdminDashboard] attribute of the "%s" dashboard controller, the route name "%s" for the "%s" action is not valid. It can only contain letter, numbers, dashes, and underscores.', $dashboardFqcn, $customRouteConfig['routeName'], $action));
            }

            if (isset($customRouteConfig['routePath']) && \in_array($action, ['edit', 'detail', 'delete'], true) && !str_contains($customRouteConfig['routePath'], '{entityId}')) {
                throw new \RuntimeException(sprintf('In the #[AdminDashboard] attribute of the "%s" dashboard controller, the path for the "%s" action must contain the "{entityId}" placeholder.', $action, $dashboardFqcn));
            }
        }

        return array_replace_recursive(self::DEFAULT_ROUTES_CONFIG, $customRoutesConfig);
    }

    private function getDashboardsRouteConfig(): array
    {
        $config = [];

        foreach ($this->dashboardControllers as $dashboardController) {
            $reflectionClass = new \ReflectionClass($dashboardController);
            $indexMethod = $reflectionClass->getMethod('index');

            // for BC reasons, the Symfony Route attribute is available under two different namespaces;
            // true first the recommended namespace and then fall back to the legacy namespace
            $attributes = $indexMethod->getAttributes('Symfony\Component\Routing\Attribute\Route');
            if ([] === $attributes) {
                $attributes = $indexMethod->getAttributes('Symfony\Component\Routing\Annotation\Route');
            }

            if ([] === $attributes) {
                throw new \RuntimeException(sprintf('When using pretty URLs, the "%s" EasyAdmin dashboard controller must define its route configuration (route name and path) using Symfony\'s #[Route] attribute applied to its "index()" method.', $reflectionClass->getName()));
            }

            if (\count($attributes) > 1) {
                throw new \RuntimeException(sprintf('When using pretty URLs, the "%s" EasyAdmin dashboard controller must define only one #[Route] attribute applied on its "index()" method.', $reflectionClass->getName()));
            }

            $routeAttribute = $attributes[0]->newInstance();
            $config[$reflectionClass->getName()] = [
                'routeName' => $routeAttribute->getName(),
                'routePath' => rtrim($routeAttribute->getPath(), '/'),
            ];
        }

        return $config;
    }

    private function getCrudControllerRouteConfig(string $crudControllerFqcn): array
    {
        $crudControllerConfig = [];

        $reflectionClass = new \ReflectionClass($crudControllerFqcn);
        $attributes = $reflectionClass->getAttributes(AdminCrud::class);
        $attribute = $attributes[0] ?? null;

        // first, check if the CRUD controller defines a custom route config in the #[AdminCrud] attribute
        if (null !== $attribute) {
            /** @var AdminCrud $attributeInstance */
            $attributeInstance = $attribute->newInstance();

            if (\count(array_diff(array_keys($attribute->getArguments()), ['routePath', 'routeName', 0, 1])) > 0) {
                throw new \RuntimeException(sprintf('In the #[AdminCrud] attribute of the "%s" CRUD controller, the route configuration defines some unsupported keys. You can only define these keys: "routePath" and "routeName".', $crudControllerFqcn));
            }

            if (null !== $attributeInstance->routePath) {
                $crudControllerConfig['routePath'] = trim($attributeInstance->routePath, '/');
            }

            if (null !== $attributeInstance->routeName) {
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $attributeInstance->routeName)) {
                    throw new \RuntimeException(sprintf('In the #[AdminCrud] attribute of the "%s" CRUD controller, the route name "%s" is not valid. It can only contain letter, numbers, dashes, and underscores.', $crudControllerFqcn, $attributeInstance->routeName));
                }

                $crudControllerConfig['routeName'] = trim($attributeInstance->routeName, '_');
            }
        }

        // if the CRUD controller doesn't define any or all of the route configuration,
        // use the default values based on the controller's class name
        if (!\array_key_exists('routePath', $crudControllerConfig)) {
            $crudControllerConfig['routePath'] = trim($this->transformCrudControllerNameToKebabCase($crudControllerFqcn), '/');
        }
        if (!\array_key_exists('routeName', $crudControllerConfig)) {
            $crudControllerConfig['routeName'] = trim($this->transformCrudControllerNameToSnakeCase($crudControllerFqcn), '_');
        }

        return $crudControllerConfig;
    }

    private function getCustomActionsConfig(string $crudControllerFqcn): array
    {
        $customActionsConfig = [];
        $reflectionClass = new \ReflectionClass($crudControllerFqcn);
        $methods = $reflectionClass->getMethods();

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(AdminAction::class);
            if ([] === $attributes) {
                continue;
            }

            $attribute = $attributes[0];
            /** @var AdminAction $attributeInstance */
            $attributeInstance = $attribute->newInstance();
            $action = $method->getName();

            if (\count(array_diff(array_keys($attribute->getArguments()), ['routePath', 'routeName', 'methods', 0, 1, 2])) > 0) {
                throw new \RuntimeException(sprintf('In the "%s" CRUD controller, the #[AdminAction] attribute applied to the "%s()" action includes some unsupported keys. You can only define these keys: "routePath", "routeName", and "methods".', $crudControllerFqcn, $action));
            }

            if (null !== $attributeInstance->routePath) {
                if (\in_array($action, ['edit', 'detail', 'delete'], true) && !str_contains($attributeInstance->routePath, '{entityId}')) {
                    throw new \RuntimeException(sprintf('In the "%s" CRUD controller, the #[AdminAction] attribute applied to the "%s()" action is missing the "{entityId}" placeholder in its route path.', $crudControllerFqcn, $action));
                }

                $customActionsConfig[$action]['routePath'] = trim($attributeInstance->routePath, '/');
            }

            if (null !== $attributeInstance->routeName) {
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $attributeInstance->routeName)) {
                    throw new \RuntimeException(sprintf('In the "%s" CRUD controller, the #[AdminAction] attribute applied to the "%s()" action defines an invalid route name: "%s". Valid route names can only contain letters, numbers, dashes, and underscores.', $crudControllerFqcn, $action, $attributeInstance->routeName));
                }

                $customActionsConfig[$action]['routeName'] = trim($attributeInstance->routeName, '_');
            }

            if (\array_key_exists('methods', $attribute->getArguments()) && null !== $attribute->getArguments()['methods'] && \in_array($action, ['index', 'new', 'edit', 'detail', 'delete'], true)) {
                throw new \RuntimeException(sprintf('In the "%s" CRUD controller, the #[AdminAction] attribute applied to the "%s()" action cannot define the "methods" argument because these are built-in EasyAdmin actions and have fixed HTTP methods.', $crudControllerFqcn, $action));
            }

            if (null !== $attributeInstance->methods) {
                $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT'];
                foreach ($attributeInstance->methods as $httpMethod) {
                    if (!\in_array(strtoupper($httpMethod), $allowedMethods, true)) {
                        throw new \RuntimeException(sprintf('In the "%s" CRUD controller, the #[AdminAction] attribute applied to the "%s()" action includes "%s" as part of its HTTP methods. However, the only allowed HTTP methods are: %s', $crudControllerFqcn, $action, $httpMethod, implode(', ', $allowedMethods)));
                    }
                }

                $customActionsConfig[$action]['methods'] = $attributeInstance->methods;
            }
        }

        return $customActionsConfig;
    }

    private function getPhpAttributeInstance(string $classFqcn, string $attributeFqcn): ?object
    {
        $reflectionClass = new \ReflectionClass($classFqcn);
        if ([] === $attributes = $reflectionClass->getAttributes($attributeFqcn)) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    // transforms 'App\Controller\Admin\FooBarBazCrudController' into 'foo-bar-baz'
    private function transformCrudControllerNameToKebabCase(string $crudControllerFqcn): string
    {
        $cleanShortName = str_replace(['CrudController', 'Controller'], '', (new \ReflectionClass($crudControllerFqcn))->getShortName());
        $snakeCaseName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $cleanShortName));

        return $snakeCaseName;
    }

    // transforms 'App\Controller\Admin\FooBarBazCrudController' into 'foo_bar_baz'
    private function transformCrudControllerNameToSnakeCase(string $crudControllerFqcn): string
    {
        $shortName = str_replace(['CrudController', 'Controller'], '', (new \ReflectionClass($crudControllerFqcn))->getShortName());

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
    }

    /**
     * @param Route[] $adminRoutes
     */
    private function saveAdminRoutesInCache(array $adminRoutes): void
    {
        // to speedup the look up of routes in different parts of the bundle,
        // we cache the admin routes in two different maps:
        // 1) $cache[route_name] => [dashboard, CRUD controller, action]
        // 2) $cache[dashboard][CRUD controller][action] => route_name
        $routeNameToFqcn = [];
        $fqcnToRouteName = [];

        // first, add the routes of all the application dashboards; this is needed because in
        // applications with multiple dashboards, EasyAdmin must be able to find the route data associated
        // to each dashboard; otherwise, the URLs of the menu items when visiting the dashboard route will be wrong
        foreach ($this->getDashboardsRouteConfig() as $dashboardFqcn => $dashboardRouteConfig) {
            $routeNameToFqcn[$dashboardRouteConfig['routeName']] = [
                EA::DASHBOARD_CONTROLLER_FQCN => $dashboardFqcn,
                EA::CRUD_CONTROLLER_FQCN => null,
                EA::CRUD_ACTION => null,
            ];
            $fqcnToRouteName[$dashboardFqcn][''][''] = $dashboardRouteConfig['routeName'];
        }

        // then, add all the generated admin routes
        foreach ($adminRoutes as $routeName => $route) {
            $routeNameToFqcn[$routeName] = [
                EA::DASHBOARD_CONTROLLER_FQCN => $route->getDefault(EA::DASHBOARD_CONTROLLER_FQCN),
                EA::CRUD_CONTROLLER_FQCN => $route->getDefault(EA::CRUD_CONTROLLER_FQCN),
                EA::CRUD_ACTION => $route->getDefault(EA::CRUD_ACTION),
            ];

            $fqcnToRouteName[$route->getDefault(EA::DASHBOARD_CONTROLLER_FQCN)][$route->getDefault(EA::CRUD_CONTROLLER_FQCN)][$route->getDefault(EA::CRUD_ACTION)] = $routeName;
        }

        $routeNameToFqcnItem = $this->cache->getItem(self::CACHE_KEY_ROUTE_TO_FQCN);
        $routeNameToFqcnItem->set($routeNameToFqcn);
        $this->cache->save($routeNameToFqcnItem);

        $fqcnToRouteNameItem = $this->cache->getItem(self::CACHE_KEY_FQCN_TO_ROUTE);
        $fqcnToRouteNameItem->set($fqcnToRouteName);
        $this->cache->save($fqcnToRouteNameItem);
    }
}

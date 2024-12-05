<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Cache;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use function Symfony\Component\String\u;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CacheWarmer implements CacheWarmerInterface
{
    public const DASHBOARD_ROUTES_CACHE = 'easyadmin/routes-dashboard.php';

    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $cacheFilename = ($buildDir ?? $cacheDir).'/'.self::DASHBOARD_ROUTES_CACHE;

        if (file_exists($cacheFilename)) {
            // this method must return an array of classes to preload, but we don't use
            // this feature, so we return an empty array
            return [];
        }

        $allRoutes = $this->router->getRouteCollection();
        $dashboardRoutes = [];

        /** @var Route $route */
        foreach ($allRoutes as $routeName => $route) {
            $controller = $route->getDefault('_controller') ?? '';
            // controller is defined as $router->add('admin', '/')->controller(DashboardController::class)
            if (\is_string($controller) && '' !== $controller && class_exists($controller)) {
                $controller .= '::__invoke';
            }

            // controller is defined as $router->add('admin', '/')->controller([DashboardController::class, 'index'])
            if (\is_array($controller)) {
                $controller = $controller[0].'::'.($controller[1] ?? '__invoke');
            }

            $controller = u($controller);
            if ($controller->isEmpty()) {
                // this happens e.g. when using 'lexik/jwt-authentication-bundle', which defines an empty controller
                continue;
            }

            if (!$controller->endsWith('::index') && !$controller->endsWith('::__invoke')) {
                continue;
            }

            $controllerFqcn = $controller->beforeLast('::')->toString();
            if (!is_subclass_of($controllerFqcn, DashboardControllerInterface::class)) {
                continue;
            }

            // when using i18n routes, the same controller can be associated to
            // multiple routes (e.g. 'admin.en', 'admin.es', 'admin.fr', etc.)
            $dashboardRoutes[$routeName] = $controller->toString();
        }

        (new Filesystem())->dumpFile(
            $cacheFilename,
            '<?php return '.var_export($dashboardRoutes, true).';'
        );

        // this method must return an array of classes to preload, but we don't use
        // this feature, so we return an empty array
        return [];
    }
}

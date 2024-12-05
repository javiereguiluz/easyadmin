<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Router;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Router\AdminRouteGeneratorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class AdminRouteLoader extends Loader
{
    public const ROUTE_LOADER_TYPE = 'easyadmin.routes';

    public function __construct(
        private AdminRouteGeneratorInterface $adminRouteGenerator,
    ) {
        parent::__construct(null);
    }

    public function supports($resource, ?string $type = null): bool
    {
        return self::ROUTE_LOADER_TYPE === $type;
    }

    public function load($resource, ?string $type = null): RouteCollection
    {
        return $this->adminRouteGenerator->generateAll();
    }
}

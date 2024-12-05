<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Router;

use Symfony\Component\Routing\RouteCollection;

/**
 * This class generates all routes for every EasyAdmin dashboard in the application
 * and provides a utility to get the Symfony route name for a given {dashboard, CRUD controller, action} tuple.
 *
 * The generated ROUTES are based on a set of default route names and paths, but
 * that can be overwritten at the dashboard, controller and method/action level
 * using the #[AdminDashboard], #[AdminCrud] and #[AdminCrud] attributes.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @method bool usesPrettyUrls()
 */
interface AdminRouteGeneratorInterface
{
    /**
     * This method is called by the custom route loader and must generate all
     * the routes for all the actions of all CRUD controllers and for all dashboards.
     */
    public function generateAll(): RouteCollection;

    public function findRouteName(string $dashboardFqcn, string $crudControllerFqcn, string $actionName): ?string;

    /**
     * This will removed in EasyAdmin 5.0, which will only use pretty URLs.
     */
    // public function usesPrettyUrls(): bool;
}

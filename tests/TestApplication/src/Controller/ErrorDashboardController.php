<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin-error', routeName: 'admin_error')]
class ErrorDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyAdmin Tests - Errors');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Error Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('ErrorFieldDoesNotBelongToAnyTabCrudController', null, ErrorFieldDoesNotBelongToAnyTabCrudController::class);
    }
}

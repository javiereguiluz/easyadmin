<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\User;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(
    routePath: '/backend/three/',
    routeName: 'admin3',
    routeOptions: [
        'requirements' => [
            'foo' => '.*',
        ],
        'options' => [
            'compiler_class' => 'Symfony\Component\Routing\RouteCompiler',
        ],
        'defaults' => [
            'foo' => 'bar',
        ],
        'host' => 'example.com',
        'methods' => ['GET', 'HEAD'],
        'schemes' => 'https',
        'condition' => 'context.getMethod() in ["GET", "HEAD"]',
        'locale' => 'es',
        'format' => 'html',
        'utf8' => true,
        'stateless' => true,
    ],
    allowedControllers: [UserCrudController::class],
)]
class ThirdDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EasyAdmin Tests');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
    }
}

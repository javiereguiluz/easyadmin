<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(
    routes: [
        'new' => ['routePath' => '/add-new', 'routeName' => 'add'],
        'edit' => ['routePath' => '/edit/---{entityId}---', 'routeName' => 'change'],
        'detail' => ['routePath' => '/show-{entityId}'],
        'delete' => ['routeName' => 'delete_this_now'],
    ],
    allowedControllers: [UserCrudController::class]
)]
class SecondDashboardController extends AbstractDashboardController
{
    #[Route('/second/dashboard', name: 'second_dashboard')]
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

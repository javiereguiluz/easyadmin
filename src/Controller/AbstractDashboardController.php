<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use function Symfony\Component\Translation\t;

/**
 * This class is useful to extend your dashboard from it instead of implementing
 * the interface.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
abstract class AbstractDashboardController extends AbstractController implements DashboardControllerInterface
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            AdminUrlGenerator::class => '?'.AdminUrlGenerator::class,
        ]);
    }

    #[Route('/admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/welcome.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new();
    }

    public function configureAssets(): Assets
    {
        return Assets::new();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(t('page_title.dashboard', domain: 'EasyAdminBundle'), 'internal:home');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $userMenuItems = [];

        if (class_exists(LogoutUrlGenerator::class)) {
            $userMenuItems[] = MenuItem::section();
            $userMenuItems[] = MenuItem::linkToLogout(t('user.sign_out', domain: 'EasyAdminBundle'), 'internal:sign-out');
        }
        if ($this->isGranted(Permission::EA_EXIT_IMPERSONATION)) {
            $userMenuItems[] = MenuItem::linkToExitImpersonation(t('user.exit_impersonation', domain: 'EasyAdminBundle'), 'internal:user-lock');
        }

        $userName = method_exists($user, '__toString') ? (string) $user : $user->getUserIdentifier();

        return UserMenu::new()
            ->displayUserName()
            ->displayUserAvatar()
            ->setName($userName)
            ->setAvatarUrl(null)
            ->setMenuItems($userMenuItems);
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureActions(): Actions
    {
        return Actions::new()
            ->addBatchAction(Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DELETE)

            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)

            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)

            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }

    public function configureFilters(): Filters
    {
        return Filters::new();
    }
}

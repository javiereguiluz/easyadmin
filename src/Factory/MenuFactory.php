<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Factory\MenuFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemMatcherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MainMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use function Symfony\Component\Translation\t;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MenuFactory implements MenuFactoryInterface
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly LogoutUrlGenerator $logoutUrlGenerator,
        private readonly AdminUrlGeneratorInterface $adminUrlGenerator,
        private readonly MenuItemMatcherInterface $menuItemMatcher,
    ) {
    }

    /**
     * @param MenuItemInterface[] $menuItems
     */
    public function createMainMenu(array $menuItems): MainMenuDto
    {
        return new MainMenuDto($this->buildMenuItems($menuItems));
    }

    public function createUserMenu(UserMenu $userMenu): UserMenuDto
    {
        $userMenuDto = $userMenu->getAsDto();
        $builtUserMenuItems = $this->buildMenuItems($userMenuDto->getItems());
        $userMenuDto->setItems($builtUserMenuItems);

        return $userMenuDto;
    }

    /**
     * @param MenuItemInterface[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function buildMenuItems(array $menuItems): array
    {
        /** @var AdminContext $adminContext */
        $adminContext = $this->adminContextProvider->getContext();
        $translationDomain = $adminContext->getI18n()->getTranslationDomain() ?? '';

        $builtItems = [];
        foreach ($menuItems as $i => $menuItem) {
            $menuItemDto = $menuItem->getAsDto();
            if (false === $this->authChecker->isGranted(Permission::EA_VIEW_MENU_ITEM, $menuItemDto)) {
                continue;
            }

            $subItems = [];
            foreach ($menuItemDto->getSubItems() as $j => $menuSubItemDto) {
                if (false === $this->authChecker->isGranted(Permission::EA_VIEW_MENU_ITEM, $menuSubItemDto)) {
                    continue;
                }

                $subItems[] = $this->buildMenuItem($menuSubItemDto, [], $translationDomain);
            }

            $builtItems[] = $this->buildMenuItem($menuItemDto, $subItems, $translationDomain);
        }

        $builtItems = $this->menuItemMatcher->markSelectedMenuItem($builtItems, $adminContext->getRequest());

        return $builtItems;
    }

    private function buildMenuItem(MenuItemDto $menuItemDto, array $subItems, string $translationDomain): MenuItemDto
    {
        if (!$menuItemDto->getLabel() instanceof TranslatableInterface) {
            $label = $menuItemDto->getLabel();
            $menuItemDto->setLabel(
                '' === $label ? $label : t($label, $menuItemDto->getTranslationParameters(), $translationDomain)
            );
        }

        $url = $this->generateMenuItemUrl($menuItemDto);
        $menuItemDto->setLinkUrl($url);

        $menuItemDto->setSubItems($subItems);

        // if menu item points to an absolute URL and no 'rel' attribute is defined,
        // assign the 'rel="noopener"' attribute for performance and security reasons.
        // see https://web.dev/external-anchors-use-rel-noopener/
        if ('' === $menuItemDto->getLinkRel() && MenuItemDto::TYPE_URL === $menuItemDto->getType()) {
            $menuItemDto->setLinkRel('noopener');
        }

        return $menuItemDto;
    }

    private function generateMenuItemUrl(MenuItemDto $menuItemDto): string
    {
        $menuItemType = $menuItemDto->getType();

        if (MenuItemDto::TYPE_CRUD === $menuItemType) {
            $routeParameters = $menuItemDto->getRouteParameters();

            $this->adminUrlGenerator
                // remove all existing query params to avoid keeping search queries, filters and pagination
                ->unsetAll()
                // set any other parameters defined by the menu item
                ->setAll($routeParameters);

            $entityFqcn = $routeParameters[EA::ENTITY_FQCN] ?? null;
            $crudControllerFqcn = $routeParameters[EA::CRUD_CONTROLLER_FQCN] ?? null;
            if (null === $entityFqcn && null === $crudControllerFqcn) {
                throw new \RuntimeException(sprintf('The CRUD menu item with label "%s" must define either the entity FQCN (using the third constructor argument) or the CRUD Controller FQCN (using the "setController()" method).', $menuItemDto->getLabel()));
            }

            // 1. if CRUD controller is defined, use it...
            if (null !== $crudControllerFqcn) {
                $this->adminUrlGenerator->setController($crudControllerFqcn);
            // 2. ...otherwise, find the CRUD controller from the entityFqcn
            } else {
                $crudControllers = $this->adminContextProvider->getContext()?->getCrudControllers();
                if (null === $controllerFqcn = $crudControllers->findCrudFqcnByEntityFqcn($entityFqcn)) {
                    throw new \RuntimeException(sprintf('Unable to find the controller related to the "%s" Entity; did you forget to extend "%s"?', $entityFqcn, AbstractCrudController::class));
                }

                $this->adminUrlGenerator->setController($controllerFqcn);
                $this->adminUrlGenerator->unset(EA::ENTITY_FQCN);
            }

            return $this->adminUrlGenerator->generateUrl();
        }

        if (MenuItemDto::TYPE_DASHBOARD === $menuItemType) {
            return $this->adminUrlGenerator->unsetAll()->generateUrl();
        }

        if (MenuItemDto::TYPE_ROUTE === $menuItemType) {
            return $this->adminUrlGenerator
                ->unsetAll()
                ->setRoute($menuItemDto->getRouteName(), $menuItemDto->getRouteParameters())
                ->generateUrl();
        }

        if (MenuItemDto::TYPE_SECTION === $menuItemType) {
            return '#';
        }

        if (MenuItemDto::TYPE_URL === $menuItemType) {
            return $menuItemDto->getLinkUrl();
        }

        if (MenuItemDto::TYPE_LOGOUT === $menuItemType) {
            return $this->logoutUrlGenerator->getLogoutPath();
        }

        if (MenuItemDto::TYPE_EXIT_IMPERSONATION === $menuItemType) {
            // the switch parameter name can be changed, but this code assumes it's always
            // the default one because Symfony doesn't provide a generic exitImpersonationUrlGenerator
            return '?_switch_user=_exit';
        }

        return '';
    }
}

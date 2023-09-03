<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Context;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\DashboardDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\I18nDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MainMenuDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\MenuFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Registry\CrudControllerRegistryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Registry\TemplateRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminContext implements AdminContextInterface
{
    private Request $request;
    private ?UserInterface $user;
    private I18nDtoInterface $i18nDto;
    private CrudControllerRegistryInterface $crudControllers;
    private ?EntityDtoInterface $entityDto;
    private DashboardDtoInterface $dashboardDto;
    private DashboardControllerInterface $dashboardControllerInstance;
    private AssetsDtoInterface $assetDto;
    private ?CrudDtoInterface $crudDto;
    private ?SearchDtoInterface $searchDto;
    private MenuFactoryInterface $menuFactory;
    private TemplateRegistryInterface $templateRegistry;
    private ?MainMenuDtoInterface $mainMenuDto = null;
    private ?UserMenuDtoInterface $userMenuDto = null;

    public function __construct(
        Request $request,
        ?UserInterface $user,
        I18nDtoInterface $i18nDto,
        CrudControllerRegistryInterface $crudControllers,
        DashboardDtoInterface $dashboardDto,
        DashboardControllerInterface $dashboardController,
        AssetsDtoInterface $assetDto,
        ?CrudDtoInterface $crudDto,
        ?EntityDtoInterface $entityDto,
        ?SearchDtoInterface $searchDto,
        MenuFactoryInterface $menuFactory,
        TemplateRegistryInterface $templateRegistry
    ) {
        $this->request = $request;
        $this->user = $user;
        $this->i18nDto = $i18nDto;
        $this->crudControllers = $crudControllers;
        $this->dashboardDto = $dashboardDto;
        $this->dashboardControllerInstance = $dashboardController;
        $this->crudDto = $crudDto;
        $this->assetDto = $assetDto;
        $this->entityDto = $entityDto;
        $this->searchDto = $searchDto;
        $this->menuFactory = $menuFactory;
        $this->templateRegistry = $templateRegistry;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getReferrer(): ?string
    {
        return $this->request->query->get(EA::REFERRER);
    }

    public function getI18n(): I18nDtoInterface
    {
        return $this->i18nDto;
    }

    public function getCrudControllers(): CrudControllerRegistryInterface
    {
        return $this->crudControllers;
    }

    public function getEntity(): EntityDtoInterface
    {
        return $this->entityDto;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getAssets(): AssetsDtoInterface
    {
        return $this->assetDto;
    }

    public function getSignedUrls(): bool
    {
        return $this->dashboardDto->getSignedUrls();
    }

    public function getAbsoluteUrls(): bool
    {
        return $this->dashboardDto->getAbsoluteUrls();
    }

    public function getDashboardTitle(): string
    {
        return $this->dashboardDto->getTitle();
    }

    public function getDashboardFaviconPath(): string
    {
        return $this->dashboardDto->getFaviconPath();
    }

    public function getDashboardControllerFqcn(): string
    {
        return \get_class($this->dashboardControllerInstance);
    }

    public function getDashboardRouteName(): string
    {
        return $this->dashboardDto->getRouteName();
    }

    public function getDashboardContentWidth(): string
    {
        return $this->dashboardDto->getContentWidth();
    }

    public function getDashboardSidebarWidth(): string
    {
        return $this->dashboardDto->getSidebarWidth();
    }

    public function getDashboardHasDarkModeEnabled(): bool
    {
        return $this->dashboardDto->isDarkModeEnabled();
    }

    public function getDashboardLocales(): array
    {
        return $this->dashboardDto->getLocales();
    }

    public function getMainMenu(): MainMenuDtoInterface
    {
        if (null !== $this->mainMenuDto) {
            return $this->mainMenuDto;
        }

        $configuredMenuItems = $this->dashboardControllerInstance->configureMenuItems();
        $mainMenuItems = \is_array($configuredMenuItems) ? $configuredMenuItems : iterator_to_array(
            $configuredMenuItems,
            false
        );

        return $this->mainMenuDto = $this->menuFactory->createMainMenu($mainMenuItems);
    }

    public function getUserMenu(): UserMenuDtoInterface
    {
        if (null !== $this->userMenuDto) {
            return $this->userMenuDto;
        }

        if (null === $this->user) {
            return UserMenu::new()->getAsDto();
        }

        $userMenu = $this->dashboardControllerInstance->configureUserMenu($this->user);

        return $this->userMenuDto = $this->menuFactory->createUserMenu($userMenu);
    }

    public function getCrud(): ?CrudDtoInterface
    {
        return $this->crudDto;
    }

    public function getSearch(): ?SearchDtoInterface
    {
        return $this->searchDto;
    }

    public function getTemplatePath(string $templateName): string
    {
        return $this->templateRegistry->get($templateName);
    }
}

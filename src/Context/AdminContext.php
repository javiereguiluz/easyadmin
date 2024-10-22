<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Context;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Factory\MenuFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\DashboardDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\I18nDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\LocaleDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MainMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Registry\CrudControllerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Registry\TemplateRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A context object that stores all the state and config of the current admin request.
 *
 * IMPORTANT: any new methods added here MUST be duplicated in the AdminContextProvider class.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class AdminContext
{
    private Request $request;
    private ?UserInterface $user;
    private I18nDto $i18nDto;
    private CrudControllerRegistry $crudControllers;
    private ?EntityDto $entityDto;
    private DashboardDto $dashboardDto;
    private DashboardControllerInterface $dashboardControllerInstance;
    private AssetsDto $assetDto;
    private ?CrudDto $crudDto;
    private ?SearchDto $searchDto;
    private MenuFactoryInterface $menuFactory;
    private TemplateRegistry $templateRegistry;
    private ?MainMenuDto $mainMenuDto = null;
    private ?UserMenuDto $userMenuDto = null;

    public function __construct(Request $request, ?UserInterface $user, I18nDto $i18nDto, CrudControllerRegistry $crudControllers, DashboardDto $dashboardDto, DashboardControllerInterface $dashboardController, AssetsDto $assetDto, ?CrudDto $crudDto, ?EntityDto $entityDto, ?SearchDto $searchDto, MenuFactoryInterface $menuFactory, TemplateRegistry $templateRegistry)
    {
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
        $referrer = $this->request->query->get(EA::REFERRER);

        return '' !== $referrer ? $referrer : null;
    }

    public function getI18n(): I18nDto
    {
        return $this->i18nDto;
    }

    public function getCrudControllers(): CrudControllerRegistry
    {
        return $this->crudControllers;
    }

    public function getEntity(): EntityDto
    {
        return $this->entityDto;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getAssets(): AssetsDto
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

    public function getDashboardDefaultColorScheme(): string
    {
        return $this->dashboardDto->getDefaultColorScheme();
    }

    /**
     * @return LocaleDto[]
     */
    public function getDashboardLocales(): array
    {
        return $this->dashboardDto->getLocales();
    }

    public function getMainMenu(): MainMenuDto
    {
        if (null !== $this->mainMenuDto) {
            return $this->mainMenuDto;
        }

        $configuredMenuItems = $this->dashboardControllerInstance->configureMenuItems();
        $mainMenuItems = \is_array($configuredMenuItems) ? $configuredMenuItems : iterator_to_array($configuredMenuItems, false);

        return $this->mainMenuDto = $this->menuFactory->createMainMenu($mainMenuItems);
    }

    public function getUserMenu(): UserMenuDto
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

    public function getCrud(): ?CrudDto
    {
        return $this->crudDto;
    }

    public function getSearch(): ?SearchDto
    {
        return $this->searchDto;
    }

    public function getTemplatePath(string $templateName): string
    {
        return $this->templateRegistry->get($templateName);
    }
}

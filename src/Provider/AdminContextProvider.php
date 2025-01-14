<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Provider;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\I18nDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MainMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\UserMenuDto;
use EasyCorp\Bundle\EasyAdminBundle\Registry\CrudControllerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

final class AdminContextProvider implements AdminContextProviderInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function hasContext(): bool
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return null !== $currentRequest && $currentRequest->attributes->has(EA::CONTEXT_REQUEST_ATTRIBUTE);
    }

    public function getContext(bool $throw = false): ?AdminContext
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (null === $currentRequest) {
            if ($throw) {
                throw new \LogicException('Cannot use the EasyAdmin context: no request is available.');
            }

            return null;
        }

        return $currentRequest->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
    }

    public function getRequest(): Request
    {
        return $this->getContext(true)->getRequest();
    }

    public function getReferrer(): ?string
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.8.11',
            'EasyAdmin URLs no longer include the referrer URL. If you still need it, you can get the referrer provided by browsers via $context->getRequest()->headers->get(\'referer\').',
            __METHOD__,
        );

        return $this->getContext(true)->getReferrer();
    }

    public function getI18n(): I18nDto
    {
        return $this->getContext(true)->getI18n();
    }

    public function getCrudControllers(): CrudControllerRegistry
    {
        return $this->getContext(true)->getCrudControllers();
    }

    public function getEntity(): EntityDto
    {
        return $this->getContext(true)->getEntity();
    }

    public function getUser(): ?UserInterface
    {
        return $this->getContext(true)->getUser();
    }

    public function getAssets(): AssetsDto
    {
        return $this->getContext(true)->getAssets();
    }

    public function getSignedUrls(): bool
    {
        return $this->getContext(true)->getSignedUrls();
    }

    public function getAbsoluteUrls(): bool
    {
        return $this->getContext(true)->getAbsoluteUrls();
    }

    public function getDashboardTitle(): string
    {
        return $this->getContext(true)->getDashboardTitle();
    }

    public function getDashboardFaviconPath(): string
    {
        return $this->getContext(true)->getDashboardFaviconPath();
    }

    public function getDashboardControllerFqcn(): string
    {
        return $this->getContext(true)->getDashboardControllerFqcn();
    }

    public function getDashboardRouteName(): string
    {
        return $this->getContext(true)->getDashboardRouteName();
    }

    public function getDashboardContentWidth(): string
    {
        return $this->getContext(true)->getDashboardContentWidth();
    }

    public function getDashboardSidebarWidth(): string
    {
        return $this->getContext(true)->getDashboardSidebarWidth();
    }

    public function getDashboardHasDarkModeEnabled(): bool
    {
        return $this->getContext(true)->getDashboardHasDarkModeEnabled();
    }

    public function getDashboardDefaultColorScheme(): string
    {
        return $this->getContext(true)->getDashboardDefaultColorScheme();
    }

    public function getDashboardLocales(): array
    {
        return $this->getContext(true)->getDashboardLocales();
    }

    public function getMainMenu(): MainMenuDto
    {
        return $this->getContext(true)->getMainMenu();
    }

    public function getUserMenu(): UserMenuDto
    {
        return $this->getContext(true)->getUserMenu();
    }

    public function getCrud(): ?CrudDto
    {
        return $this->getContext(true)->getCrud();
    }

    public function getSearch(): ?SearchDto
    {
        return $this->getContext(true)->getSearch();
    }

    public function getTemplatePath(string $templateName): string
    {
        return $this->getContext(true)->getTemplatePath($templateName);
    }

    public function usePrettyUrls(): bool
    {
        return $this->getContext(true)->usePrettyUrls();
    }
}

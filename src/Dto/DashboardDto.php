<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\BreadcrumbItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\ColorScheme;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class DashboardDto
{
    private $routeName;
    private string $faviconPath = 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⬛</text></svg>';
    private string $title = 'EasyAdmin';
    private string $translationDomain = 'messages';
    private $textDirection;
    private string $contentWidth = Crud::LAYOUT_CONTENT_DEFAULT;
    private string $sidebarWidth = Crud::LAYOUT_SIDEBAR_DEFAULT;
    private bool $signedUrls = false;
    private bool $absoluteUrls = true;
    private bool $enableDarkMode = true;
    private string $defaultColorScheme = ColorScheme::LIGHT;
    /** @var LocaleDto[] */
    private array $locales = [];
    private ?string $breadcrumbRootLabel = null;
    private ?string $breadcrumbDivider = null;
    /**
     * @var \Closure(string|BreadcrumbItem|null): (string|BreadcrumbItem|null)
     */
    private ?\Closure $breadcrumbHierarchyCallback = null;

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(/* string */ $routeName): void
    {
        if (!\is_string($routeName)) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$routeName',
                __METHOD__,
                '"string"',
                \gettype($routeName)
            );
        }

        $this->routeName = $routeName;
    }

    public function getFaviconPath(): string
    {
        return $this->faviconPath;
    }

    public function setFaviconPath(string $faviconPath): void
    {
        $this->faviconPath = $faviconPath;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getTextDirection(): ?string
    {
        return $this->textDirection;
    }

    public function setTextDirection(/* ?string */ $textDirection): void
    {
        if (!\is_string($textDirection)
            && null !== $textDirection) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$textDirection',
                __METHOD__,
                '"string" or "null"',
                \gettype($textDirection)
            );
        }

        $this->textDirection = $textDirection;
    }

    public function getContentWidth(): string
    {
        return $this->contentWidth;
    }

    public function setContentWidth(string $contentWidth): void
    {
        $this->contentWidth = $contentWidth;
    }

    public function getSidebarWidth(): string
    {
        return $this->sidebarWidth;
    }

    public function setSidebarWidth(string $sidebarWidth): void
    {
        $this->sidebarWidth = $sidebarWidth;
    }

    public function getSignedUrls(): bool
    {
        return $this->signedUrls;
    }

    public function setSignedUrls(bool $signedUrls): self
    {
        $this->signedUrls = $signedUrls;

        return $this;
    }

    public function getAbsoluteUrls(): bool
    {
        return $this->absoluteUrls;
    }

    public function setAbsoluteUrls(bool $absoluteUrls): self
    {
        $this->absoluteUrls = $absoluteUrls;

        return $this;
    }

    public function setEnableDarkMode(bool $enableDarkMode): self
    {
        $this->enableDarkMode = $enableDarkMode;

        return $this;
    }

    public function isDarkModeEnabled(): bool
    {
        return $this->enableDarkMode;
    }

    public function getDefaultColorScheme(): string
    {
        return $this->defaultColorScheme;
    }

    public function setDefaultColorScheme(string $defaultColorScheme): self
    {
        $this->defaultColorScheme = $defaultColorScheme;

        return $this;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @param LocaleDto[] $locales
     */
    public function setLocales(array $locales): void
    {
        $this->locales = $locales;
    }

    public function setBreadcrumbRootLabel(?string $breadcrumbRootLabel): void
    {
        $this->breadcrumbRootLabel = $breadcrumbRootLabel;
    }

    public function getBreadcrumbRootLabel(): ?string
    {
        return $this->breadcrumbRootLabel;
    }

    public function setBreadcrumbDivider(?string $breadcrumbDivider): void
    {
        $this->breadcrumbDivider = $breadcrumbDivider;
    }

    public function getBreadcrumbDivider(): ?string
    {
        return null === $this->breadcrumbDivider ? null
            : (str_starts_with($this->breadcrumbDivider, 'url')
                ? $this->breadcrumbDivider
                : "'{$this->breadcrumbDivider}'")
        ;
    }

    /**
     * @param \Closure(string|BreadcrumbItem|null): (string|BreadcrumbItem|null) $callback
     */
    public function setBreadcrumbHierarchyCallback(?\Closure $callback): void
    {
        $this->breadcrumbHierarchyCallback = $callback;
    }

    /**
     * @return \Closure(string|BreadcrumbItem|null): (string|BreadcrumbItem|null)
     */
    public function getBreadcrumbHierarchyCallback(): \Closure
    {
        return $this->breadcrumbHierarchyCallback ?? $this->getDefaultBreadcrumbHierarchyCallback();
    }

    /**
     * @return \Closure(string|BreadcrumbItem|null): (string|BreadcrumbItem|null)
     */
    public function getDefaultBreadcrumbHierarchyCallback(): \Closure
    {
        return function (string|BreadcrumbItem|null $action): ?string {
            if ($action instanceof BreadcrumbItem) {
                $action = $action->action;
            }

            return match ($action) {
                Action::INDEX => null,
                Action::NEW => Action::INDEX,
                Action::DETAIL => Action::INDEX,
                Action::EDIT => Action::DETAIL,
                default => Action::INDEX,
            };
        };
    }
}

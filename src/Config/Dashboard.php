<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\ColorScheme;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextDirection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\DashboardDto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class Dashboard
{
    private DashboardDto $dto;

    private function __construct(DashboardDto $dashboardDto)
    {
        $this->dto = $dashboardDto;
    }

    public static function new(): self
    {
        $dto = new DashboardDto();

        return new self($dto);
    }

    public function setFaviconPath(string $path): self
    {
        $this->dto->setFaviconPath($path);

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->dto->setTitle($title);

        return $this;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->dto->setTranslationDomain($translationDomain);

        return $this;
    }

    public function setTextDirection(string $direction): self
    {
        if (!\in_array($direction, [TextDirection::LTR, TextDirection::RTL], true)) {
            throw new \InvalidArgumentException(sprintf('The "%s" value given to the textDirection option is not valid. It can only be "%s" or "%s"', $direction, TextDirection::LTR, TextDirection::RTL));
        }

        $this->dto->setTextDirection($direction);

        return $this;
    }

    public function renderContentMaximized(bool $maximized = true): self
    {
        $this->dto->setContentWidth($maximized ? Crud::LAYOUT_CONTENT_FULL : Crud::LAYOUT_CONTENT_DEFAULT);

        return $this;
    }

    public function renderSidebarMinimized(bool $minimized = true): self
    {
        $this->dto->setSidebarWidth($minimized ? Crud::LAYOUT_SIDEBAR_COMPACT : Crud::LAYOUT_SIDEBAR_DEFAULT);

        return $this;
    }

    public function disableUrlSignatures(bool $disableSignatures = true): self
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.1.0',
            'EasyAdmin URLs no longer include signatures because they don\'t provide any additional security. You can stop calling the "%s" method to disable them. This method will be removed in future EasyAdmin versions.',
            __METHOD__,
        );

        $this->dto->setSignedUrls(!$disableSignatures);

        return $this;
    }

    public function generateRelativeUrls(bool $relativeUrls = true): self
    {
        $this->dto->setAbsoluteUrls(!$relativeUrls);

        return $this;
    }

    public function disableDarkMode(bool $disableDarkMode = true): self
    {
        $this->dto->setEnableDarkMode(!$disableDarkMode);

        return $this;
    }

    public function setDefaultColorScheme(string $colorScheme): self
    {
        if (!\in_array($colorScheme, [ColorScheme::LIGHT, ColorScheme::DARK, ColorScheme::AUTO], true)) {
            throw new \InvalidArgumentException(sprintf('The "%s" value given to the colorScheme option is not valid. It can only be "%s", "%s" or "%s"', $colorScheme, ColorScheme::LIGHT, ColorScheme::DARK, ColorScheme::AUTO));
        }

        $this->dto->setDefaultColorScheme($colorScheme);

        return $this;
    }

    public function setLocales(array $locales): self
    {
        $localeDtos = [];
        foreach ($locales as $key => $value) {
            $locale = match (true) {
                $value instanceof Locale => $value,
                \is_string($key) => Locale::new($key, (string) $value),
                default => Locale::new((string) $value),
            };

            $localeDtos[] = $locale->getAsDto();
        }

        $this->dto->setLocales($localeDtos);

        return $this;
    }

    public function enableBreadcrumb(string $breadcrumbRootLabel = 'Home', ?string $breadcrumbDivider = null): self
    {
        $this->dto->setBreadcrumbRootLabel($breadcrumbRootLabel);
        $this->dto->setBreadcrumbDivider($breadcrumbDivider);

        return $this;
    }

    public function setBreadcrumbRootLabel(string $breadcrumbRootLabel): self
    {
        $this->dto->setBreadcrumbRootLabel($breadcrumbRootLabel);

        return $this;
    }

    public function setBreadcrumbDivider(?string $breadcrumbDivider): self
    {
        $this->dto->setBreadcrumbDivider($breadcrumbDivider);

        return $this;
    }

    /**
     * @param \Closure(string|BreadcrumbItem|null): (string|BreadcrumbItem|null) $callback
     */
    public function setBreadcrumbHierarchyCallback(?\Closure $callback): self
    {
        $this->dto->setBreadcrumbHierarchyCallback($callback);

        return $this;
    }

    public function getAsDto(): DashboardDto
    {
        return $this->dto;
    }
}

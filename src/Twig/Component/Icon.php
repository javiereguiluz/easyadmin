<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Twig\Component;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\IconDto;

class Icon
{
    public ?string $name = null;
    private string $iconsDir = __DIR__.'/../../../assets/icons';
    private ?string $iconSet = null;

    public function __construct(
        private AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    public function isBuiltInIconSet(): bool
    {
        return IconSet::Custom !== $this->getIconSet();
    }

    public function isFontAwesomeIconSet(): bool
    {
        return IconSet::Custom !== $this->getIconSet();
    }

    public function getIcon(): IconDto
    {
        return $this->getIconDto(trim($this->name), $this->getIconSet());
    }

    private function getIconSet(): string
    {
        return $this->iconSet ?? ($this->iconSet = $this->adminContextProvider->getContext()?->getAssets()->getIconSet() ?? IconSet::FontAwesome);
    }

    private function getDefaultIconPrefix(): string
    {
        return $this->adminContextProvider->getContext()?->getAssets()->getDefaultIconPrefix() ?? '';
    }

    private function getIconDto(string $iconName, string $iconSet): IconDto
    {
        if (str_starts_with($iconName, IconSet::Internal.':')) {
            return $this->getInternalIcon($iconName);
        }

        if ('' !== $defaultIconPrefix = $this->getDefaultIconPrefix()) {
            $iconName = sprintf('%s:%s', $defaultIconPrefix, $iconName);
        }

        return IconDto::new(name: $iconName, iconSet: $iconSet);
    }

    private function getInternalIcon(string $internalIconName): IconDto
    {
        [$iconPrefix, $iconName] = explode(':', $internalIconName);
        $iconFilePath = sprintf('%s/%s/%s.svg', $this->iconsDir, $iconPrefix, $iconName);
        if (!file_exists($iconFilePath)) {
            throw new \RuntimeException(sprintf('The icon "%s" does not exist. Check the icon name spelling and make sure that the "%s.svg" file exists in the "assets/icons/internal/ directory of EasyAdmin".', $internalIconName, $iconName));
        }

        return IconDto::new(name: $internalIconName, path: $iconFilePath, svgContents: file_get_contents($iconFilePath), iconSet: IconSet::Internal);
    }
}

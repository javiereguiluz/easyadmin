<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class IconDto
{
    private ?string $name = null;
    private ?string $path = null;
    private ?string $svgContents = null;
    private ?string $iconSet = null;

    private function __construct()
    {
    }

    public static function new(?string $name = null, ?string $path = null, ?string $svgContents = null, ?string $iconSet = null): self
    {
        $dto = new self();
        $dto->name = $name;
        $dto->path = $path;
        $dto->svgContents = $svgContents;
        $dto->iconSet = $iconSet;

        return $dto;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getSvgContents(): ?string
    {
        return $this->svgContents;
    }

    public function getIconSet(): ?string
    {
        return $this->iconSet;
    }

    public function isFontAwesomeIconSet(): bool
    {
        return IconSet::FontAwesome === $this->iconSet;
    }
}

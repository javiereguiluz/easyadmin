<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MainMenuDto
{
    private array $items;

    /**
     * @param MenuItemDto[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return MenuItemDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}

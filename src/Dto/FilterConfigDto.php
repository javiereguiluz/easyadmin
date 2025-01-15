<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FilterConfigDto
{
    private KeyValueStore $filters;

    public function __construct()
    {
        $this->filters = KeyValueStore::new();
    }

    public function addFilter(FilterInterface|string $filterNameOrConfig): void
    {
        $this->filters->set((string) $filterNameOrConfig, $filterNameOrConfig);
    }

    public function getFilter(string $propertyName): FilterInterface|string|null
    {
        return $this->filters->get($propertyName);
    }

    public function all(): array
    {
        return $this->filters->all();
    }
}

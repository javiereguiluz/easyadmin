<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class ScopeButtonDto
{
    private string $name = '';
    private TranslatableInterface|string $label = '';
    /**
     * @var ScopeFilterDto[]
     */
    private array $filters = [];
    private bool $active = false;

    public function addFilter(ScopeFilterDto|string $propertyName, string|array|null $value = null, string $comparison = ComparisonType::EQ, ?string $value2 = null): self
    {
        $this->filters[] = $propertyName instanceof ScopeFilterDto ? $propertyName : new ScopeFilterDto($propertyName, $value, $comparison, $value2);

        return $this;
    }

    public function unsetFilter(string $propertyName): self
    {
        $this->filters[] = new ScopeFilterDto($propertyName, null, '=', null, true);

        return $this;
    }

    public function getLabel(): TranslatableInterface|string
    {
        return $this->label;
    }

    public function setLabel(TranslatableInterface|string $label): void
    {
        $this->label = $label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ScopeFilterDto[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param ScopeFilterDto[] $filters
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function hasFilterWithPropertyName(string $propertyName)
    {
        return \in_array($propertyName, array_map(fn (ScopeFilterDto $filter) => $filter->getPropertyName(), $this->getFilters()), true);
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}

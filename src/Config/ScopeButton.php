<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ScopeButtonDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ScopeFilterDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class ScopeButton
{
    private ScopeButtonDto $dto;

    private function __construct()
    {
        $this->dto = new ScopeButtonDto();
    }

    public static function new(string $name, TranslatableInterface|string $label): self
    {
        return (new self())
            ->setName($name)
            ->setLabel($label);
    }

    public function addFilter(ScopeFilterDto|string $propertyName, string|array|null $value = null, string $comparison = ComparisonType::EQ, ?string $value2 = null): self
    {
        if ($this->dto->hasFilterWithPropertyName($propertyName instanceof ScopeFilterDto ? $propertyName->getPropertyName() : $propertyName)) {
            throw new \InvalidArgumentException(sprintf('There is already a filter with the name "%s".', $propertyName));
        }
        $this->dto->addFilter($propertyName, $value, $comparison, $value2);

        return $this;
    }

    public function unsetFilter(string $propertyName): self
    {
        $this->dto->unsetFilter($propertyName);

        return $this;
    }

    public function setLabel(TranslatableInterface|string $label): self
    {
        $this->dto->setLabel($label);

        return $this;
    }

    public function setName(string $Name): self
    {
        $this->dto->setName($Name);

        return $this;
    }

    public function getName(): string
    {
        return $this->dto->getName();
    }

    public function getAsDto(): ScopeButtonDto
    {
        return $this->dto;
    }
}

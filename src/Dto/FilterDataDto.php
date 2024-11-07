<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FilterDataDto
{
    private int $index;
    private string $entityAlias;
    private FilterDto $filterDto;
    private $comparison;
    private mixed $value;
    private mixed $value2;

    private function __construct()
    {
    }

    public static function new(int $index, FilterDto $filterDto, string $entityAlias, array $formData): self
    {
        $filterData = new self();
        $filterData->index = $index;
        $filterData->filterDto = $filterDto;
        $filterData->entityAlias = $entityAlias;
        $filterData->comparison = $formData['comparison'];
        $filterData->value = $formData['value'];
        $filterData->value2 = $formData['value2'] ?? null;

        return $filterData;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getEntityAlias(): string
    {
        return $this->entityAlias;
    }

    public function getProperty(): string
    {
        return $this->filterDto->getProperty();
    }

    public function getFormTypeOption(string $optionName)
    {
        return $this->filterDto->getFormTypeOption($optionName);
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getValue2(): mixed
    {
        return $this->value2;
    }

    public function getParameterName(): string
    {
        return sprintf('%s_%d', str_replace('.', '_', $this->getProperty()), $this->index);
    }

    public function getParameter2Name(): string
    {
        return sprintf('%s_%d', str_replace('.', '_', $this->getProperty()), $this->index + 1);
    }
}

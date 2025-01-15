<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FilterDto
{
    private ?string $fqcn = null;
    private ?string $formType = null;
    private KeyValueStore $formTypeOptions;
    private ?string $propertyName = null;
    private $label;
    private $applyCallable;

    public function __construct()
    {
        $this->formTypeOptions = KeyValueStore::new();
    }

    public function getFqcn(): ?string
    {
        return $this->fqcn;
    }

    public function setFqcn(string $fqcn): void
    {
        $this->fqcn = $fqcn;
    }

    public function getFormType(): ?string
    {
        return $this->formType;
    }

    public function getFormTypeOptions(): array
    {
        return $this->formTypeOptions->all();
    }

    public function getFormTypeOption(string $optionName)
    {
        return $this->formTypeOptions->get($optionName);
    }

    public function setFormTypeOptions(array $formTypeOptions): void
    {
        $this->formTypeOptions->setAll($formTypeOptions);
    }

    public function setFormTypeOption(string $optionName, mixed $optionValue): void
    {
        $this->formTypeOptions->set($optionName, $optionValue);
    }

    public function setFormTypeOptionIfNotSet(string $optionName, mixed $optionValue): void
    {
        $this->formTypeOptions->setIfNotSet($optionName, $optionValue);
    }

    public function setFormType(string $formType): void
    {
        $this->formType = $formType;
    }

    public function getProperty(): string
    {
        return $this->propertyName;
    }

    public function setProperty(string $propertyName): void
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return TranslatableInterface|string|false|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public function setLabel(TranslatableInterface|string|bool|null $label): void
    {
        if (true === $label) {
            throw new \InvalidArgumentException(sprintf('The value passed to the label of the "%s" filter is not valid. When passing boolean values, you can only pass a false value (to hide the label) but you passed a true value.', $this->propertyName));
        }

        $this->label = $label;
        // needed to also display the label in the form associated to the filter
        $this->setFormTypeOption('label', $label);
    }

    public function setApplyCallable(callable $callable): void
    {
        $this->applyCallable = $callable;
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        \call_user_func($this->applyCallable, $queryBuilder, $filterDataDto, $fieldDto, $entityDto);
    }
}

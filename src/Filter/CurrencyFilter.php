<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;

final class CurrencyFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(CurrencyType::class)
            ;
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        ?FieldDto $fieldDto,
        EntityDto $entityDto
    ): void {
        $alias         = $filterDataDto->getEntityAlias();
        $property      = $filterDataDto->getProperty();
        $comparison    = $filterDataDto->getComparison();
        $parameterName = $filterDataDto->getParameterName();
        $value         = $filterDataDto->getValue();

        $queryBuilder->andWhere(\sprintf('%s.%s %s (:%s)', $alias, $property, $comparison, $parameterName))
            ->setParameter($parameterName, $value)
        ;
    }
}

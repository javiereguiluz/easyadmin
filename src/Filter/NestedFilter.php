<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;

/**
 * @author Brandon Marcachi <brandon.marcachi@gmail.com>
 */
final class NestedFilter implements FilterInterface
{
    use FilterTrait;

    public const FORM_OPTION_WRAPPED_FILTER = 'attr.wrapped_filter';

    public const PATH_SEPARATOR_EXPECTED = '.';
    public const PATH_SEPARATOR = '_';

    /** @var FilterInterface */
    private $wrappedFilter;

    public static function new(string $propertyName, string $label = null): self
    {
        throw new \RuntimeException('Instead of this method, use the "wrap()" method.');
    }

    public static function wrap(FilterInterface $filter): FilterInterface
    {
        $filterDto = $filter->getAsDto();
        $property = $filterDto->getProperty();

        if (false === strpos($property, self::PATH_SEPARATOR_EXPECTED)) {
            return $filter;
        }

        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($property)
            ->setFormType($filterDto->getFormType())
            ->setFormTypeOptions($filterDto->getFormTypeOptions())
            ->setWrappedFilter($filter)
        ;
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $propertyPath = $filterDataDto->getProperty();

        [$targetClassMetadata, $targetProperty] = self::extractTargets(
            $queryBuilder->getEntityManager(),
            $entityDto->getFqcn(),
            $propertyPath
        );

        $wrappedEntityDto = new EntityDto($targetClassMetadata->getName(), $targetClassMetadata);
        $wrappedFilter = $this->getWrappedFilter();
        $wrappedFilterDto = $wrappedFilter->getAsDto();
        $wrappedFilterDto->setProperty($targetProperty);

        // Apply required left joins and get the alias we have to work with
        $alias = $this->applyLeftJoins($queryBuilder, $filterDataDto->getEntityAlias(), $propertyPath);

        // Recreate FilterDataDto adapted for the wrapped filter
        $wrappedFilterDataDto = FilterDataDto::new($filterDataDto->getIndex(), $wrappedFilterDto, $alias, [
            'value' => $filterDataDto->getValue(),
            'value2' => $filterDataDto->getValue2(),
            'comparison' => $filterDataDto->getComparison(),
        ]);

        $wrappedFilterDto->apply($queryBuilder, $wrappedFilterDataDto, null, $wrappedEntityDto);
    }

    public static function extractTargets(ObjectManager $objectManager, string $class, string $propertyPath): array
    {
        $segments = explode(self::PATH_SEPARATOR, $propertyPath);
        $metadata = $objectManager->getClassMetadata($class);
        $lastIndex = \count($segments) - 1;
        $property = null;

        foreach ($segments as $i => $prop) {
            if (!$metadata->hasField($prop) && !$metadata->hasAssociation($prop)) {
                self::throwInvalidPropertyPathException($propertyPath, $class);
            }

            // The target property must be at the end of path
            if ($i === $lastIndex) {
                $property = $prop;
                break;
            }

            if (!$metadata->hasAssociation($prop)) {
                self::throwInvalidPropertyPathException($propertyPath, $class);
            }

            // Move to next nested class
            $metadata = $objectManager->getClassMetadata($metadata->getAssociationTargetClass($prop));
        }

        return [$metadata, $property];
    }

    public function setWrappedFilter(FilterInterface $filter): self
    {
        $this->wrappedFilter = $filter;

        return $this->setFormTypeOption(self::FORM_OPTION_WRAPPED_FILTER, $filter);
    }

    public function getWrappedFilter(): FilterInterface
    {
        return $this->wrappedFilter;
    }

    public function setProperty(string $propertyName): self
    {
        // Replace dots with underscore to avoid errors
        $this->dto->setProperty(
            str_replace(self::PATH_SEPARATOR_EXPECTED, self::PATH_SEPARATOR, $propertyName)
        );

        return $this;
    }

    private function applyLeftJoins(QueryBuilder $qb, string $alias, string $propertyPath): string
    {
        $path = explode(self::PATH_SEPARATOR, $propertyPath);
        $lastIndex = \count($path) - 1;
        $currentAlias = $alias;

        foreach ($path as $i => $prop) {
            if ($i === $lastIndex) {
                break;
            }

            $nextAlias = sprintf('%s_%s', $currentAlias, $prop);
            if (!\in_array($nextAlias, $qb->getAllAliases(), true)) {
                $qb->leftJoin(sprintf('%s.%s', $currentAlias, $prop), $nextAlias);
            }

            $currentAlias = $nextAlias;
        }

        return $currentAlias;
    }

    private static function throwInvalidPropertyPathException(string $propertyPath, string $class): void
    {
        throw new \InvalidArgumentException(sprintf(
            'The property path "%s" for class "%s" is invalid.',
            str_replace(self::PATH_SEPARATOR, self::PATH_SEPARATOR_EXPECTED, $propertyPath),
            $class
        ));
    }
}

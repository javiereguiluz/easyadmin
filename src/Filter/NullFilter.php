<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NullFilterType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class NullFilter implements FilterInterface
{
    use FilterTrait;

    public const CHOICE_VALUE_NULL = 'null';
    public const CHOICE_VALUE_NOT_NULL = 'not_null';

    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(NullFilterType::class);
    }

    public function setChoiceLabels(string|TranslatableInterface $nullChoiceLabel, string|TranslatableInterface $notNullChoiceLabel): self
    {
        if (
            $nullChoiceLabel instanceof TranslatableInterface
            || $notNullChoiceLabel instanceof TranslatableInterface
        ) {
            $this->dto->setFormTypeOption('choices', [
                self::CHOICE_VALUE_NULL,
                self::CHOICE_VALUE_NOT_NULL,
            ]);
            $this->dto->setFormTypeOption(
                'choice_label',
                fn ($value) => self::CHOICE_VALUE_NULL === $value ? $nullChoiceLabel : $notNullChoiceLabel,
            );
        } else {
            $this->dto->setFormTypeOption('choices', [
                $nullChoiceLabel => self::CHOICE_VALUE_NULL,
                $notNullChoiceLabel => self::CHOICE_VALUE_NOT_NULL,
            ]);
        }

        return $this;
    }

    public function apply(QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto): void
    {
        $comparison = self::CHOICE_VALUE_NULL === $filterDataDto->getValue() ? 'IS' : 'IS NOT';
        $queryBuilder
            ->andWhere(sprintf('%s.%s %s NULL', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty(), $comparison));
    }
}

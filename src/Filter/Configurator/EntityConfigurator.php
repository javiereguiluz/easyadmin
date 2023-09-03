<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Filter\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityConfigurator implements FilterConfiguratorInterface
{
    public function supports(
        FilterDtoInterface $filterDto,
        ?FieldDtoInterface $fieldDto,
        EntityDtoInterface $entityDto,
        AdminContext $context
    ): bool {
        return EntityFilter::class === $filterDto->getFqcn();
    }

    public function configure(
        FilterDtoInterface $filterDto,
        ?FieldDtoInterface $fieldDto,
        EntityDtoInterface $entityDto,
        AdminContext $context
    ): void {
        $propertyName = $filterDto->getProperty();
        if (!$entityDto->isAssociation($propertyName)) {
            return;
        }

        $doctrineMetadata = $entityDto->getPropertyMetadata($propertyName);
        // TODO: add the 'em' form type option too?
        $filterDto->setFormTypeOptionIfNotSet('value_type_options.class', $doctrineMetadata->get('targetEntity'));
        $filterDto->setFormTypeOptionIfNotSet(
            'value_type_options.multiple',
            $entityDto->isToManyAssociation($propertyName)
        );
        $filterDto->setFormTypeOptionIfNotSet('value_type_options.attr.data-ea-widget', 'ea-autocomplete');

        if ($entityDto->isToOneAssociation($propertyName)) {
            // don't show the 'empty value' placeholder when all join columns are required,
            // because an empty filter value would always return no result
            $numberOfRequiredJoinColumns = \count(
                array_filter(
                    $doctrineMetadata->get('joinColumns'),
                    static fn(array $joinColumn): bool => false === ($joinColumn['nullable'] ?? false)
                )
            );

            $someJoinColumnsAreNullable = \count(
                    $doctrineMetadata->get('joinColumns')
                ) !== $numberOfRequiredJoinColumns;

            if ($someJoinColumnsAreNullable) {
                $filterDto->setFormTypeOptionIfNotSet('value_type_options.placeholder', 'label.form.empty_value');
            }
        }
    }
}

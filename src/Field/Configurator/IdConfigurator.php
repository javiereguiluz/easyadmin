<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\CrudInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

use function Symfony\Component\String\u;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class IdConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        return IdField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDtoInterface $field, EntityDtoInterface $entityDto, AdminContext $context): void
    {
        $maxLength = $field->getCustomOption(IdField::OPTION_MAX_LENGTH);
        if (null === $maxLength) {
            $maxLength = CrudInterface::PAGE_INDEX === $context->getCrud()->getCurrentPage() ? 7 : -1;
        }

        if (-1 !== $maxLength && null !== $field->getValue()) {
            $field->setFormattedValue(u($field->getValue())->truncate($maxLength, '…')->toString());
        }
    }
}

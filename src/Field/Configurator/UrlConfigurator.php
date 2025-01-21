<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use function Symfony\Component\String\u;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class UrlConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return UrlField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $field->setFormTypeOptionIfNotSet('attr.inputmode', 'url');
        $field->setFormTypeOptionIfNotSet('default_protocol', $field->getCustomOption(UrlField::OPTION_DEFAULT_PROTOCOL));

        $prettyUrl = str_replace(['http://www.', 'https://www.', 'http://', 'https://'], '', (string) $field->getValue());
        $prettyUrl = rtrim($prettyUrl, '/');

        if (Action::INDEX === $context->getCrud()->getCurrentAction()) {
            $prettyUrl = u($prettyUrl)->truncate(32, '…')->toString();
        }

        $field->setFormattedValue($prettyUrl);
    }
}

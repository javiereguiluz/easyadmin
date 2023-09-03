<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CodeEditorConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        return CodeEditorField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDtoInterface $field, EntityDtoInterface $entityDto, AdminContext $context): void
    {
        if ('rtl' === $context->getI18n()->getTextDirection()) {
            $field->addCssAsset(Asset::fromEasyAdminAssetPackage('field-code-editor.rtl.css')->getAsDto());
        }
    }
}

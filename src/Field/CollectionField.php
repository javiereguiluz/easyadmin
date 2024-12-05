<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CollectionField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ALLOW_ADD = 'allowAdd';
    public const OPTION_ALLOW_DELETE = 'allowDelete';
    public const OPTION_ENTRY_IS_COMPLEX = 'entryIsComplex';
    public const OPTION_ENTRY_TYPE = 'entryType';
    public const OPTION_ENTRY_TO_STRING_METHOD = 'entryToStringMethod';
    public const OPTION_SHOW_ENTRY_LABEL = 'showEntryLabel';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';
    public const OPTION_ENTRY_USES_CRUD_FORM = 'entryUsesCrudController';
    public const OPTION_ENTRY_CRUD_CONTROLLER_FQCN = 'entryCrudControllerFqcn';
    public const OPTION_ENTRY_CRUD_NEW_PAGE_NAME = 'entryCrudNewPageName';
    public const OPTION_ENTRY_CRUD_EDIT_PAGE_NAME = 'entryCrudEditPageName';

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/collection')
            ->setFormType(CollectionType::class)
            ->addCssClass('field-collection')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-collection.js')->onlyOnForms())
            ->setDefaultColumns('col-md-8 col-xxl-7')
            ->setCustomOption(self::OPTION_ALLOW_ADD, true)
            ->setCustomOption(self::OPTION_ALLOW_DELETE, true)
            ->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, null)
            ->setCustomOption(self::OPTION_ENTRY_TYPE, null)
            ->setCustomOption(self::OPTION_ENTRY_TO_STRING_METHOD, null)
            ->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, false)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false)
            ->setCustomOption(self::OPTION_ENTRY_USES_CRUD_FORM, false)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_CONTROLLER_FQCN, null)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, null)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, null);
    }

    public function allowAdd(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_ADD, $allow);

        return $this;
    }

    public function allowDelete(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_DELETE, $allow);

        return $this;
    }

    /**
     * Set this option to TRUE if the collection items are complex form types
     * composed of several form fields (EasyAdmin applies a special rendering to make them look better).
     */
    public function setEntryIsComplex(bool $isComplex = true): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, $isComplex);

        return $this;
    }

    public function setEntryType(string $formTypeFqcn): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_TYPE, $formTypeFqcn);

        return $this;
    }

    /**
     * @param string|callable $toStringMethod Either a string with the name of the method to call in the entry object or a callable to generate the string representation of the entry. The callable is passed the value as the first argument and the translator service as the second argument.
     */
    public function setEntryToStringMethod(string|callable $toStringMethod): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_TO_STRING_METHOD, $toStringMethod);

        return $this;
    }

    public function showEntryLabel(bool $showLabel = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, $showLabel);

        return $this;
    }

    public function renderExpanded(bool $renderExpanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $renderExpanded);

        return $this;
    }

    public function useEntryCrudForm(?string $crudControllerFqcn = null, ?string $crudNewPageName = null, ?string $crudEditPageName = null): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_USES_CRUD_FORM, true);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_CONTROLLER_FQCN, $crudControllerFqcn);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, $crudNewPageName);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, $crudEditPageName);

        return $this;
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use Symfony\Contracts\Translation\TranslatableInterface;

interface FieldTraitAwareInterface extends FieldInterface
{
    public function setFieldFqcn(string $fieldFqcn): self;

    public function setProperty(string $propertyName): self;

    public function setLabel(TranslatableInterface|string|false|null $label): self;

    public function setValue($value): self;

    public function setFormattedValue($value): self;

    public function formatValue(?callable $callable): self;

    public function setVirtual(bool $isVirtual): self;

    public function setDisabled(bool $disabled = true): self;

    public function setRequired(bool $isRequired): self;

    public function setEmptyData($emptyData = null): self;

    public function setFormType(string $formTypeFqcn): self;

    public function setFormTypeOptions(array $options): self;

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOption(string $optionName, $optionValue): self;

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOptionIfNotSet(string $optionName, $optionValue): self;

    public function setSortable(bool $isSortable): self;

    public function setPermission(string $permission): self;

    /**
     * @param string $textAlign It can be 'left', 'center' or 'right'
     */
    public function setTextAlign(string $textAlign): self;

    public function setHelp(TranslatableInterface|string $help): self;

    public function addCssClass(string $cssClass): self;

    public function setCssClass(string $cssClass): self;

    public function setTranslationParameters(array $parameters): self;

    public function setTemplateName(string $name): self;

    public function setTemplatePath(string $path): self;

    public function addFormTheme(string ...$formThemePaths): self;

    public function addWebpackEncoreEntries(Asset|string ...$entryNamesOrAssets): self;

    public function addCssFiles(Asset|string ...$pathsOrAssets): self;

    public function addJsFiles(Asset|string ...$pathsOrAssets): self;

    public function addHtmlContentsToHead(string ...$contents): self;

    public function addHtmlContentsToBody(string ...$contents): self;

    public function setCustomOption(string $optionName, $optionValue): self;

    public function setCustomOptions(array $options): self;

    public function hideOnDetail(): self;

    public function hideOnForm(): self;

    public function hideWhenCreating(): self;

    public function hideWhenUpdating(): self;

    public function hideOnIndex(): self;

    public function onlyOnDetail(): self;

    public function onlyOnForms(): self;

    public function onlyOnIndex(): self;

    public function onlyWhenCreating(): self;

    public function onlyWhenUpdating(): self;

    /**
     * @param int|string $cols An integer with the number of columns that this field takes (e.g. 6),
     *                         or a string with responsive col CSS classes (e.g. 'col-6 col-sm-4 col-lg-3')
     */
    public function setColumns(int|string $cols): self;

    /**
     * Used to define the columns of fields when users don't define the
     * columns explicitly using the setColumns() method.
     * This should only be used if you create a custom EasyAdmin field,
     * not when configuring fields in your backend.
     *
     * @internal
     */
    public function setDefaultColumns(int|string $cols): self;

    public function setIcon(?string $iconCssClass, string $invokingMethod = 'FormField::setIcon()'): self;
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
trait FieldTrait
{
    private FieldDto $dto;

    private function __construct()
    {
        $this->dto = new FieldDto();
    }

    public function __clone(): void
    {
        $this->dto = clone $this->dto;
    }

    public function setFieldFqcn(string $fieldFqcn): self
    {
        $this->dto->setFieldFqcn($fieldFqcn);

        return $this;
    }

    public function setProperty(string $propertyName): self
    {
        $this->dto->setProperty($propertyName);

        return $this;
    }

    public function setPropertySuffix(string $propertyNameSuffix): self
    {
        if ('' === trim($propertyNameSuffix, " \t\n\r\0\v")) {
            throw new \InvalidArgumentException('The suffix cannot be empty.');
        }

        if (!FormConfigBuilder::isValidName($propertyNameSuffix)) {
            throw new \InvalidArgumentException(sprintf('The suffix "%s" is not valid. You must follow form name conventions.', $propertyNameSuffix));
        }

        $this->dto->setPropertyNameSuffix($propertyNameSuffix);

        return $this;
    }

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public function setLabel($label): self
    {
        $this->dto->setLabel($label);

        return $this;
    }

    public function setValue($value): self
    {
        $this->dto->setValue($value);

        return $this;
    }

    public function setFormattedValue($value): self
    {
        $this->dto->setFormattedValue($value);

        return $this;
    }

    public function formatValue(?callable $callable): self
    {
        $this->dto->setFormatValueCallable($callable);

        return $this;
    }

    public function setVirtual(bool $isVirtual): self
    {
        $this->dto->setVirtual($isVirtual);

        return $this;
    }

    public function setDisabled(bool $disabled = true): self
    {
        $this->dto->setFormTypeOption('disabled', $disabled);

        return $this;
    }

    public function setRequired(bool $isRequired): self
    {
        $this->dto->setFormTypeOption('required', $isRequired);

        return $this;
    }

    public function setEmptyData($emptyData = null): self
    {
        $this->dto->setFormTypeOption('empty_data', $emptyData);

        return $this;
    }

    public function setFormType(string $formTypeFqcn): self
    {
        $this->dto->setFormType($formTypeFqcn);

        return $this;
    }

    public function setFormTypeOptions(array $options): self
    {
        $this->dto->setFormTypeOptions($options);

        return $this;
    }

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOption(string $optionName, $optionValue): self
    {
        $this->dto->setFormTypeOption($optionName, $optionValue);

        return $this;
    }

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOptionIfNotSet(string $optionName, $optionValue): self
    {
        $this->dto->setFormTypeOptionIfNotSet($optionName, $optionValue);

        return $this;
    }

    /**
     * Sets the value of a custom HTML attribute that will be added when rendering the field.
     * E.g. setHtmlAttribute('data-foo', 'bar') will render a 'data-foo="bar"' attribute in HTML.
     * On 'index' and 'detail' pages, the attribute is added to the field container (<td> and div.field-group respectively).
     * On 'new' and 'edit' pages, the attribute is added to the form field; it's a shortcut for the equivalent setFormTypeOption('attr.data-foo', 'bar).
     */
    public function setHtmlAttribute(string $attributeName, $attributeValue): self
    {
        if (str_contains($attributeName, '.')) {
            throw new \InvalidArgumentException(sprintf('Field attributes must be defined as pairs of "(string) key => (scalar) value". That\'s why the attribute name ("%s") cannot use the "dot notation" with the "." character to create nested attributes.', $attributeName));
        }

        if (!\is_scalar($attributeValue)) {
            throw new \InvalidArgumentException(sprintf('The value of the "%s" attribute must be a scalar value (string, integer, float, boolean); "%s" given.', $attributeName, \gettype($attributeValue)));
        }

        $this->dto->setHtmlAttribute($attributeName, $attributeValue);
        $this->dto->setFormTypeOption('attr.'.$attributeName, $attributeValue);

        return $this;
    }

    public function setHtmlAttributes(array $attributes): self
    {
        foreach ($attributes as $attributeName => $attributeValue) {
            $this->setHtmlAttribute($attributeName, $attributeValue);
        }

        return $this;
    }

    public function setSortable(bool $isSortable): self
    {
        $this->dto->setSortable($isSortable);

        return $this;
    }

    public function setPermission(string|Expression $permission): self
    {
        $this->dto->setPermission($permission);

        return $this;
    }

    /**
     * @param string $textAlign It can be 'left', 'center' or 'right'
     */
    public function setTextAlign(string $textAlign): self
    {
        $validOptions = [TextAlign::LEFT, TextAlign::CENTER, TextAlign::RIGHT];
        if (!\in_array($textAlign, $validOptions, true)) {
            throw new \InvalidArgumentException(sprintf('The value of the "textAlign" option can only be one of these: "%s" ("%s" was given).', implode(',', $validOptions), $textAlign));
        }

        $this->dto->setTextAlign($textAlign);

        return $this;
    }

    public function setHelp(TranslatableInterface|string $help): self
    {
        $this->dto->setHelp($help);

        return $this;
    }

    public function addCssClass(string $cssClass): self
    {
        $this->dto->setCssClass($this->dto->getCssClass().' '.$cssClass);

        return $this;
    }

    public function setCssClass(string $cssClass): self
    {
        $this->dto->setCssClass($cssClass);

        return $this;
    }

    public function setTranslationParameters(array $parameters): self
    {
        $this->dto->setTranslationParameters($parameters);

        return $this;
    }

    public function setTemplateName(string $name): self
    {
        $this->dto->setTemplateName($name);
        $this->dto->setTemplatePath(null);

        return $this;
    }

    public function setTemplatePath(string $path): self
    {
        $this->dto->setTemplateName(null);
        $this->dto->setTemplatePath($path);

        return $this;
    }

    public function addFormTheme(string ...$formThemePaths): self
    {
        foreach ($formThemePaths as $formThemePath) {
            $this->dto->addFormTheme($formThemePath);
        }

        return $this;
    }

    public function addWebpackEncoreEntries(Asset|string ...$entryNamesOrAssets): self
    {
        if (!class_exists('Symfony\\WebpackEncoreBundle\\Twig\\EntryFilesTwigExtension')) {
            throw new \RuntimeException('You are trying to add Webpack Encore entries in a field but Webpack Encore is not installed in your project. Try running "composer require symfony/webpack-encore-bundle"');
        }

        foreach ($entryNamesOrAssets as $entryNameOrAsset) {
            if (\is_string($entryNameOrAsset)) {
                $this->dto->addWebpackEncoreAsset(new AssetDto($entryNameOrAsset));
            } else {
                $this->dto->addWebpackEncoreAsset($entryNameOrAsset->getAsDto());
            }
        }

        return $this;
    }

    public function addAssetMapperEntries(Asset|string ...$entryNamesOrAssets): self
    {
        if (!class_exists('Symfony\\Component\\AssetMapper\\AssetMapper')) {
            throw new \RuntimeException('You are trying to add AssetMapper entries in a field but AssetMapper is not installed in your project. Try running "composer require symfony/asset-mapper"');
        }

        foreach ($entryNamesOrAssets as $entryNameOrAsset) {
            if (\is_string($entryNameOrAsset)) {
                $this->dto->addAssetMapperEncoreAsset(new AssetDto($entryNameOrAsset));
            } else {
                $this->dto->addAssetMapperEncoreAsset($entryNameOrAsset->getAsDto());
            }
        }

        return $this;
    }

    public function addCssFiles(Asset|string ...$pathsOrAssets): self
    {
        foreach ($pathsOrAssets as $pathOrAsset) {
            if (\is_string($pathOrAsset)) {
                $this->dto->addCssAsset(new AssetDto($pathOrAsset));
            } else {
                $this->dto->addCssAsset($pathOrAsset->getAsDto());
            }
        }

        return $this;
    }

    public function addJsFiles(Asset|string ...$pathsOrAssets): self
    {
        foreach ($pathsOrAssets as $pathOrAsset) {
            if (\is_string($pathOrAsset)) {
                $this->dto->addJsAsset(new AssetDto($pathOrAsset));
            } else {
                $this->dto->addJsAsset($pathOrAsset->getAsDto());
            }
        }

        return $this;
    }

    public function addHtmlContentsToHead(string ...$contents): self
    {
        foreach ($contents as $content) {
            $this->dto->addHtmlContentToHead($content);
        }

        return $this;
    }

    public function addHtmlContentsToBody(string ...$contents): self
    {
        foreach ($contents as $content) {
            $this->dto->addHtmlContentToBody($content);
        }

        return $this;
    }

    public function setCustomOption(string $optionName, $optionValue): self
    {
        $this->dto->setCustomOption($optionName, $optionValue);

        return $this;
    }

    public function setCustomOptions(array $options): self
    {
        $this->dto->setCustomOptions($options);

        return $this;
    }

    public function hideOnDetail(): self
    {
        $displayedOn = $this->dto->getDisplayedOn();
        $displayedOn->delete(Crud::PAGE_DETAIL);

        $this->dto->setDisplayedOn($displayedOn);

        return $this;
    }

    public function hideOnForm(): self
    {
        $displayedOn = $this->dto->getDisplayedOn();
        $displayedOn->delete(Crud::PAGE_NEW);
        $displayedOn->delete(Crud::PAGE_EDIT);

        $this->dto->setDisplayedOn($displayedOn);

        return $this;
    }

    public function hideWhenCreating(): self
    {
        $displayedOn = $this->dto->getDisplayedOn();
        $displayedOn->delete(Crud::PAGE_NEW);

        $this->dto->setDisplayedOn($displayedOn);

        return $this;
    }

    public function hideWhenUpdating(): self
    {
        $displayedOn = $this->dto->getDisplayedOn();
        $displayedOn->delete(Crud::PAGE_EDIT);

        $this->dto->setDisplayedOn($displayedOn);

        return $this;
    }

    public function hideOnIndex(): self
    {
        $displayedOn = $this->dto->getDisplayedOn();
        $displayedOn->delete(Crud::PAGE_INDEX);

        $this->dto->setDisplayedOn($displayedOn);

        return $this;
    }

    public function onlyOnDetail(): self
    {
        $this->dto->setDisplayedOn(KeyValueStore::new([Crud::PAGE_DETAIL => Crud::PAGE_DETAIL]));

        return $this;
    }

    public function onlyOnForms(): self
    {
        $this->dto->setDisplayedOn(KeyValueStore::new([
            Crud::PAGE_NEW => Crud::PAGE_NEW,
            Crud::PAGE_EDIT => Crud::PAGE_EDIT,
        ]));

        return $this;
    }

    public function onlyOnIndex(): self
    {
        $this->dto->setDisplayedOn(KeyValueStore::new([Crud::PAGE_INDEX => Crud::PAGE_INDEX]));

        return $this;
    }

    public function onlyWhenCreating(): self
    {
        $this->dto->setDisplayedOn(KeyValueStore::new([Crud::PAGE_NEW => Crud::PAGE_NEW]));

        return $this;
    }

    public function onlyWhenUpdating(): self
    {
        $this->dto->setDisplayedOn(KeyValueStore::new([Crud::PAGE_EDIT => Crud::PAGE_EDIT]));

        return $this;
    }

    /**
     * @param int|string $cols An integer with the number of columns that this field takes (e.g. 6),
     *                         or a string with responsive col CSS classes (e.g. 'col-6 col-sm-4 col-lg-3')
     */
    public function setColumns(int|string $cols): self
    {
        $this->dto->setColumns(\is_int($cols) ? 'col-md-'.$cols : $cols);

        return $this;
    }

    /**
     * Used to define the columns of fields when users don't define the
     * columns explicitly using the setColumns() method.
     * This should only be used if you create a custom EasyAdmin field,
     * not when configuring fields in your backend.
     *
     * @internal
     */
    public function setDefaultColumns(int|string $cols): self
    {
        $this->dto->setDefaultColumns(\is_int($cols) ? 'col-md-'.$cols : $cols);

        return $this;
    }

    public function getAsDto(): FieldDto
    {
        return $this->dto;
    }
}

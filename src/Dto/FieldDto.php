<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EaFormFieldsetType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnGroupCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnGroupOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabListType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneGroupCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneGroupOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneOpenType;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FieldDto
{
    private ?string $fieldFqcn = null;
    private ?string $propertyName = null;
    private ?string $propertyNameSuffix = null;
    private mixed $value = null;
    private mixed $formattedValue = null;
    private $formatValueCallable;
    private $label;
    private ?string $formType = null;
    private KeyValueStore $formTypeOptions;
    private ?bool $sortable = null;
    private ?bool $virtual = null;
    private string|Expression|null $permission = null;
    private ?string $textAlign = null;
    private $help;
    private string $cssClass = '';
    // how many columns the field takes when rendering
    // (defined as Bootstrap 5 grid classes; e.g. 'col-md-6 col-xxl-3')
    private ?string $columns = null;
    // same as $columns but used when the user doesn't define columns explicitly
    private string $defaultColumns = '';
    private array $translationParameters = [];
    private ?string $templateName = 'crud/field/text';
    private ?string $templatePath = null;
    private array $formThemePaths = [];
    private AssetsDto $assets;
    private KeyValueStore $customOptions;
    private KeyValueStore $doctrineMetadata;
    /** @internal */
    private $uniqueId;
    private KeyValueStore $displayedOn;
    private array $htmlAttributes = [];

    public function __construct()
    {
        $this->uniqueId = new Ulid();
        $this->assets = new AssetsDto();
        $this->formTypeOptions = KeyValueStore::new();
        $this->customOptions = KeyValueStore::new();
        $this->doctrineMetadata = KeyValueStore::new();
        $this->displayedOn = KeyValueStore::new([
            Crud::PAGE_INDEX => Crud::PAGE_INDEX,
            Crud::PAGE_DETAIL => Crud::PAGE_DETAIL,
            Crud::PAGE_EDIT => Crud::PAGE_EDIT,
            Crud::PAGE_NEW => Crud::PAGE_NEW,
        ]);
    }

    public function __clone()
    {
        $this->uniqueId = new Ulid();
        $this->assets = clone $this->assets;
        $this->formTypeOptions = clone $this->formTypeOptions;
        $this->customOptions = clone $this->customOptions;
        $this->doctrineMetadata = clone $this->doctrineMetadata;
        $this->displayedOn = clone $this->displayedOn;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public function isFormDecorationField(): bool
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.8.0',
            '"FieldDto::isFormDecorationField()" has been deprecated in favor of "FieldDto::isFormLayoutField()" and it will be removed in 5.0.0.',
        );

        return $this->isFormLayoutField();
    }

    public function isFormLayoutField(): bool
    {
        $formLayoutFieldClasses = [
            EaFormTabListType::class,
            EaFormTabPaneGroupOpenType::class,
            EaFormTabPaneGroupCloseType::class,
            EaFormTabPaneOpenType::class,
            EaFormTabPaneCloseType::class,
            EaFormColumnGroupOpenType::class,
            EaFormColumnGroupCloseType::class,
            EaFormColumnOpenType::class,
            EaFormColumnCloseType::class,
            EaFormFieldsetOpenType::class,
            EaFormFieldsetCloseType::class,
        ];

        return \in_array($this->formType, $formLayoutFieldClasses, true);
    }

    public function isFormFieldset(): bool
    {
        return \in_array($this->formType, [EaFormFieldsetType::class, EaFormFieldsetOpenType::class], true);
    }

    public function isFormTab(): bool
    {
        return EaFormTabPaneOpenType::class === $this->formType;
    }

    public function isFormColumn(): bool
    {
        return EaFormColumnOpenType::class === $this->formType;
    }

    public function getFieldFqcn(): ?string
    {
        return $this->fieldFqcn;
    }

    /**
     * @internal Don't use this method yourself. EasyAdmin uses it internally
     *           to set the field FQCN. It's OK to use getFieldFqcn() to get this value.
     */
    public function setFieldFqcn(string $fieldFqcn): void
    {
        $this->fieldFqcn = $fieldFqcn;
    }

    public function getProperty(): string
    {
        return $this->propertyName;
    }

    public function setProperty(string $propertyName): void
    {
        $this->propertyName = $propertyName;
    }

    public function getPropertyNameSuffix(): ?string
    {
        return $this->propertyNameSuffix;
    }

    public function setPropertyNameSuffix(?string $propertyNameSuffix): void
    {
        $this->propertyNameSuffix = $propertyNameSuffix;
    }

    public function getPropertyNameWithSuffix(): string
    {
        return sprintf(
            '%s%s%s',
            $this->propertyName,
            null !== $this->propertyNameSuffix ? '_' : '',
            $this->propertyNameSuffix ?? '',
        );
    }

    /**
     * Returns the original unmodified value stored in the entity field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Returns the value to be displayed for the field (it could be the
     * same as the value stored in the field or not).
     */
    public function getFormattedValue(): mixed
    {
        return $this->formattedValue;
    }

    public function setFormattedValue(mixed $formattedValue): void
    {
        $this->formattedValue = $formattedValue;
    }

    public function getFormatValueCallable(): ?callable
    {
        return $this->formatValueCallable;
    }

    public function setFormatValueCallable(?callable $callable): void
    {
        $this->formatValueCallable = $callable;
    }

    /**
     * @return TranslatableInterface|string|false|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public function setLabel($label): void
    {
        if (!\is_string($label) && !$label instanceof TranslatableInterface && false !== $label && null !== $label) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$label',
                __METHOD__,
                '"TranslatableInterface", "string", "false" or "null"',
                \gettype($label)
            );
        }

        $this->label = $label;
    }

    public function getFormType(): ?string
    {
        return $this->formType;
    }

    public function setFormType(string $formTypeFqcn): void
    {
        $this->formType = $formTypeFqcn;
    }

    public function getFormTypeOptions(): array
    {
        return $this->formTypeOptions->all();
    }

    public function getFormTypeOption(string $optionName)
    {
        return $this->formTypeOptions->get($optionName);
    }

    public function setFormTypeOptions(array $formTypeOptions): void
    {
        foreach ($formTypeOptions as $optionName => $optionValue) {
            $this->setFormTypeOption($optionName, $optionValue);
        }
    }

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOption(string $optionName, mixed $optionValue): void
    {
        $this->formTypeOptions->set($optionName, $optionValue);
    }

    /**
     * @param string $optionName You can use "dot" notation to set nested options (e.g. 'attr.class')
     */
    public function setFormTypeOptionIfNotSet(string $optionName, mixed $optionValue): void
    {
        $this->formTypeOptions->setIfNotSet($optionName, $optionValue);
    }

    public function isSortable(): ?bool
    {
        return $this->sortable;
    }

    public function setSortable(bool $isSortable): void
    {
        $this->sortable = $isSortable;
    }

    public function isVirtual(): ?bool
    {
        return $this->virtual;
    }

    public function setVirtual(bool $isVirtual): void
    {
        $this->virtual = $isVirtual;
    }

    public function getTextAlign(): ?string
    {
        return $this->textAlign;
    }

    public function setTextAlign(string $textAlign): void
    {
        $this->textAlign = $textAlign;
    }

    public function getPermission(): string|Expression|null
    {
        return $this->permission;
    }

    public function setPermission(string|Expression $permission): void
    {
        $this->permission = $permission;
    }

    public function getHelp(): TranslatableInterface|string|null
    {
        return $this->help;
    }

    public function setHelp(TranslatableInterface|string $help): void
    {
        $this->help = $help;
    }

    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    public function setCssClass(string $cssClass): void
    {
        $this->cssClass = trim($cssClass);
    }

    public function getColumns(): ?string
    {
        return $this->columns;
    }

    public function setColumns(?string $columnCssClasses): void
    {
        $this->columns = $columnCssClasses;
    }

    public function getDefaultColumns(): string
    {
        return $this->defaultColumns;
    }

    public function setDefaultColumns(string $columnCssClasses): void
    {
        $this->defaultColumns = $columnCssClasses;
    }

    public function getTranslationParameters(): array
    {
        return $this->translationParameters;
    }

    public function setTranslationParameters(array $translationParameters): void
    {
        $this->translationParameters = $translationParameters;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getTemplatePath(): ?string
    {
        return $this->templatePath;
    }

    public function setTemplatePath(?string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    public function addFormTheme(string $formThemePath): void
    {
        $this->formThemePaths[] = $formThemePath;
    }

    public function getFormThemes(): array
    {
        return $this->formThemePaths;
    }

    public function setFormThemes(array $formThemePaths): void
    {
        $this->formThemePaths = $formThemePaths;
    }

    public function getAssets(): AssetsDto
    {
        return $this->assets;
    }

    public function setAssets(AssetsDto $assets): void
    {
        $this->assets = $assets;
    }

    public function addAssetMapperEncoreAsset(AssetDto $assetDto): void
    {
        $this->assets->addAssetMapperAsset($assetDto);
    }

    public function addWebpackEncoreAsset(AssetDto $assetDto): void
    {
        $this->assets->addWebpackEncoreAsset($assetDto);
    }

    public function addCssAsset(AssetDto $assetDto): void
    {
        $this->assets->addCssAsset($assetDto);
    }

    public function addJsAsset(AssetDto $assetDto): void
    {
        $this->assets->addJsAsset($assetDto);
    }

    public function addHtmlContentToHead(string $htmlContent): void
    {
        $this->assets->addHtmlContentToHead($htmlContent);
    }

    public function addHtmlContentToBody(string $htmlContent): void
    {
        $this->assets->addHtmlContentToBody($htmlContent);
    }

    public function getCustomOptions(): KeyValueStore
    {
        return $this->customOptions;
    }

    public function getCustomOption(string $optionName): mixed
    {
        return $this->customOptions->get($optionName);
    }

    public function setCustomOptions(array $customOptions): void
    {
        $this->customOptions = KeyValueStore::new($customOptions);
    }

    public function setCustomOption(string $optionName, mixed $optionValue): void
    {
        $this->customOptions->set($optionName, $optionValue);
    }

    public function getDoctrineMetadata(): KeyValueStore
    {
        return $this->doctrineMetadata;
    }

    public function setDoctrineMetadata(array $metadata): void
    {
        $this->doctrineMetadata = KeyValueStore::new($metadata);
    }

    public function getDisplayedOn(): KeyValueStore
    {
        return $this->displayedOn;
    }

    public function setDisplayedOn(KeyValueStore $displayedOn): void
    {
        $this->displayedOn = $displayedOn;
    }

    public function isDisplayedOn(string $pageName): bool
    {
        return $this->displayedOn->has($pageName);
    }

    public function getHtmlAttributes(): array
    {
        return $this->htmlAttributes;
    }

    public function setHtmlAttributes(array $htmlAttributes): self
    {
        $this->htmlAttributes = $htmlAttributes;

        return $this;
    }

    public function setHtmlAttribute(string $attribute, mixed $value): self
    {
        $this->htmlAttributes[$attribute] = $value;

        return $this;
    }
}

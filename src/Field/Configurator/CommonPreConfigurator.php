<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\ActionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\CrudInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

use function Symfony\Component\String\u;
use function Symfony\Component\Translation\t;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CommonPreConfigurator implements FieldConfiguratorInterface
{
    private PropertyAccessorInterface $propertyAccessor;
    private EntityFactoryInterface $entityFactory;

    public function __construct(PropertyAccessorInterface $propertyAccessor, EntityFactoryInterface $entityFactory)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->entityFactory = $entityFactory;
    }

    public function supports(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        // this configurator applies to all kinds of properties
        return true;
    }

    public function configure(FieldDtoInterface $field, EntityDtoInterface $entityDto, AdminContext $context): void
    {
        $translationDomain = $context->getI18n()->getTranslationDomain();

        // if a field already has set a value, someone has written something to
        // it (as a virtual field or overwrite); don't modify the value in that case
        $isReadable = true;
        if (null === $value = $field->getValue()) {
            try {
                $value = null === $entityDto->getInstance() ? null : $this->propertyAccessor->getValue(
                    $entityDto->getInstance(),
                    $field->getProperty()
                );
            } catch (AccessException|UnexpectedTypeException) {
                $isReadable = false;
            }

            $field->setValue($value);
            if (null === $field->getFormattedValue()) {
                $field->setFormattedValue($value);
            }
        }

        $label = $this->buildLabelOption($field, $translationDomain, $context->getCrud()->getCurrentPage());
        $field->setLabel($label);

        $isRequired = $this->buildRequiredOption($field, $entityDto);
        $field->setFormTypeOption('required', $isRequired);

        $isSortable = $this->buildSortableOption($field, $entityDto);
        $field->setSortable($isSortable);

        $isVirtual = $this->buildVirtualOption($field, $entityDto);
        $field->setVirtual($isVirtual);

        $templatePath = $this->buildTemplatePathOption($context, $field, $entityDto, $isReadable);
        $field->setTemplatePath($templatePath);

        $doctrineMetadata = $entityDto->hasProperty($field->getProperty()) ? $entityDto->getPropertyMetadata(
            $field->getProperty()
        )->all() : [];
        $field->setDoctrineMetadata($doctrineMetadata);

        if (null !== $helpMessage = $this->buildHelpOption($field, $translationDomain)) {
            $field->setHelp($helpMessage);
            $field->setFormTypeOptionIfNotSet('help', $helpMessage);
            $field->setFormTypeOptionIfNotSet('help_html', true);
        }

        if ('' !== $field->getCssClass()) {
            $field->setFormTypeOptionIfNotSet('row_attr.class', $field->getCssClass());
        }

        if (null !== $field->getTextAlign()) {
            $field->setFormTypeOptionIfNotSet('attr.data-ea-align', $field->getTextAlign());
        }

        $field->setFormTypeOptionIfNotSet('label', $field->getLabel());
    }

    private function buildHelpOption(FieldDtoInterface $field, string $translationDomain): ?TranslatableInterface
    {
        $help = $field->getHelp();
        if (null === $help || $help instanceof TranslatableInterface) {
            return $help;
        }

        return '' === $help ? null : t($help, $field->getTranslationParameters(), $translationDomain);
    }

    /**
     * @return TranslatableInterface|string|false|null
     */
    private function buildLabelOption(FieldDtoInterface $field, string $translationDomain, ?string $currentPage)
    {
        // don't autogenerate a label for these special fields (there's a dedicated configurator for them)
        if (FormField::class === $field->getFieldFqcn()) {
            $label = $field->getLabel();

            if ($label instanceof TranslatableInterface) {
                return $label;
            }

            return (null === $label || false === $label || '' === $label) ? $label : t(
                $label,
                $field->getTranslationParameters(),
                $translationDomain
            );
        }

        // if an Avatar field doesn't define its label, don't autogenerate it for the 'index' page
        // (because the table of the 'index' page looks better without a header in the avatar column)
        if (ActionInterface::INDEX === $currentPage && null === $field->getLabel(
            ) && AvatarField::class === $field->getFieldFqcn()) {
            $field->setLabel(false);
        }

        // it field doesn't define its label explicitly, generate an automatic
        // label based on the field's field name
        if (null === $label = $field->getLabel()) {
            $label = $this->humanizeString($field->getProperty());
        }

        if ('' === $label) {
            return $label;
        }

        // don't translate labels in form-related pages because Symfony Forms translates
        // labels automatically and that causes false "translation is missing" errors
        if (\in_array($currentPage, [CrudInterface::PAGE_EDIT, CrudInterface::PAGE_NEW], true)) {
            return $label;
        }

        if ($label instanceof TranslatableInterface) {
            return $label;
        }

        return t($label, $field->getTranslationParameters(), $translationDomain);
    }

    private function buildSortableOption(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        if (null !== $isSortable = $field->isSortable()) {
            return $isSortable;
        }

        return $entityDto->hasProperty($field->getProperty());
    }

    private function buildVirtualOption(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        return !$entityDto->hasProperty($field->getProperty());
    }

    private function buildTemplatePathOption(
        AdminContext $adminContext,
        FieldDtoInterface $field,
        EntityDtoInterface $entityDto,
        bool $isReadable
    ): string {
        if (null !== $templatePath = $field->getTemplatePath()) {
            return $templatePath;
        }

        // if field has a value set, don't display it as inaccessible (needed e.g. for virtual fields)
        if (!$isReadable && null === $field->getValue()) {
            return $adminContext->getTemplatePath('label/inaccessible');
        }

        if (null === $templateName = $field->getTemplateName()) {
            throw new \RuntimeException(
                sprintf(
                    'Fields must define either their templateName or their templatePath. None given for "%s" field.',
                    $field->getProperty()
                )
            );
        }

        return $adminContext->getTemplatePath($templateName);
    }

    private function buildRequiredOption(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        if (null !== $isRequired = $field->getFormTypeOption('required')) {
            return $isRequired;
        }

        // consider that virtual properties are not required
        if (!$entityDto->hasProperty($field->getProperty())) {
            return false;
        }

        $doctrinePropertyMetadata = $entityDto->getPropertyMetadata($field->getProperty());

        // If at least one join column of an association field isn't nullable then the field is "required" by default, otherwise the field is optional
        if ($entityDto->isAssociation($field->getProperty())) {
            $associatedEntityMetadata = $this->entityFactory->getEntityMetadata(
                $doctrinePropertyMetadata->get('targetEntity')
            );
            foreach ($doctrinePropertyMetadata->get('joinColumns', []) as $joinColumn) {
                $propertyNameInAssociatedEntity = $joinColumn['referencedColumnName'];
                $associatedPropertyMetadata = $associatedEntityMetadata->fieldMappings[$propertyNameInAssociatedEntity] ?? [];
                $isNullable = $associatedPropertyMetadata['nullable'] ?? true;
                if (false === $isNullable) {
                    return true;
                }
            }

            return false;
        }

        // TODO: check if it's correct to never make a boolean value required
        // I guess it's correct because Symfony Forms treat NULL as FALSE by default (i.e. in the database the value won't be NULL)
        if ('boolean' === $doctrinePropertyMetadata->get('type')) {
            return false;
        }

        return false === $doctrinePropertyMetadata->get('nullable');
    }

    private function humanizeString(string $string): string
    {
        $uString = u($string);
        $upperString = $uString->upper()->toString();

        // this prevents humanizing all-uppercase labels (e.g. 'UUID' -> 'U u i d')
        // and other special labels which look better in uppercase
        if ($uString->toString() === $upperString || \in_array($upperString, ['ID', 'URL'], true)) {
            return $upperString;
        }

        return $uString
            ->replaceMatches('/([A-Z])/', '_$1')
            ->replaceMatches('/[_\s]+/', ' ')
            ->trim()
            ->lower()
            ->title(true)
            ->toString();
    }
}

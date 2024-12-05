<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EaFormRowType;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FieldFactory
{
    private static array $doctrineTypeToFieldFqcn = [
        'array' => ArrayField::class, // don't use Types::ARRAY because it was removed in Doctrine ORM 3.0
        Types::BIGINT => TextField::class,
        Types::BINARY => TextareaField::class,
        Types::BLOB => TextareaField::class,
        Types::BOOLEAN => BooleanField::class,
        Types::DATE_MUTABLE => DateField::class,
        Types::DATE_IMMUTABLE => DateField::class,
        Types::DATEINTERVAL => TextField::class,
        Types::DATETIME_MUTABLE => DateTimeField::class,
        Types::DATETIME_IMMUTABLE => DateTimeField::class,
        Types::DATETIMETZ_MUTABLE => DateTimeField::class,
        Types::DATETIMETZ_IMMUTABLE => DateTimeField::class,
        Types::DECIMAL => NumberField::class,
        Types::FLOAT => NumberField::class,
        Types::GUID => TextField::class,
        Types::INTEGER => IntegerField::class,
        Types::JSON => TextField::class,
        'object' => TextField::class, // don't use Types::OBJECT because it was removed in Doctrine ORM 3.0
        Types::SIMPLE_ARRAY => ArrayField::class,
        Types::SMALLINT => IntegerField::class,
        Types::STRING => TextField::class,
        Types::TEXT => TextareaField::class,
        Types::TIME_MUTABLE => TimeField::class,
        Types::TIME_IMMUTABLE => TimeField::class,
    ];

    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly iterable $fieldConfigurators,
        private readonly FormLayoutFactory $fieldLayoutFactory)
    {
    }

    public function processFields(EntityDto $entityDto, FieldCollection $fields): void
    {
        $this->preProcessFields($fields, $entityDto);

        $context = $this->adminContextProvider->getContext();
        $currentPage = $context->getCrud()->getCurrentPage();

        $isDetailOrIndex = \in_array($currentPage, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true);
        foreach ($fields as $fieldDto) {
            if ((null !== $currentPage && false === $fieldDto->isDisplayedOn($currentPage))
                || false === $this->authorizationChecker->isGranted(Permission::EA_VIEW_FIELD, $fieldDto)) {
                $fields->unset($fieldDto);

                continue;
            }

            // "form rows" only make sense in pages that contain forms
            if ($isDetailOrIndex && EaFormRowType::class === $fieldDto->getFormType()) {
                $fields->unset($fieldDto);

                continue;
            }

            // when creating new entities with "useEntryCrudForm" on an edit page we must
            // explicitly check for the "new" page because $currentPage will be "edit"
            if ((null === $entityDto->getInstance()) && !$fieldDto->isDisplayedOn(Crud::PAGE_NEW)) {
                $fields->unset($fieldDto);

                continue;
            }

            foreach ($this->fieldConfigurators as $configurator) {
                if (!$configurator->supports($fieldDto, $entityDto)) {
                    continue;
                }

                $configurator->configure($fieldDto, $entityDto, $context);
            }

            // check again if the field is displayed because this can change in the configurators
            if (null !== $currentPage && false === $fieldDto->isDisplayedOn($currentPage)) {
                $fields->unset($fieldDto);
                continue;
            }

            foreach ($fieldDto->getFormThemes() as $formThemePath) {
                $context?->getCrud()?->addFormTheme($formThemePath);
            }

            $fields->set($fieldDto);
        }

        if (!$fields->isEmpty()) {
            $this->fieldLayoutFactory->createLayout($fields, $this->adminContextProvider->getContext()?->getCrud()?->getCurrentPage() ?? Crud::PAGE_INDEX);
        }

        $entityDto->setFields($fields);
    }

    private function preProcessFields(FieldCollection $fields, EntityDto $entityDto): void
    {
        if ($fields->isEmpty()) {
            return;
        }

        foreach ($fields as $fieldDto) {
            if (Field::class !== $fieldDto->getFieldFqcn()) {
                continue;
            }

            // this is a virtual field, so we can't autoconfigure it
            if (!$entityDto->hasProperty($fieldDto->getProperty())) {
                continue;
            }

            if ($fieldDto->getProperty() === $entityDto->getPrimaryKeyName()) {
                $guessedFieldFqcn = IdField::class;
            } else {
                $doctrinePropertyType = $entityDto->getPropertyMetadata($fieldDto->getProperty())->get('type');
                $guessedFieldFqcn = self::$doctrineTypeToFieldFqcn[$doctrinePropertyType] ?? null;

                if (null === $guessedFieldFqcn) {
                    throw new \RuntimeException(sprintf('The Doctrine type of the "%s" field is "%s", which is not supported by EasyAdmin. For Doctrine\'s Custom Mapping Types have a look at EasyAdmin\'s field docs.', $fieldDto->getProperty(), $doctrinePropertyType));
                }
            }

            $fields->set($this->transformField($fieldDto, $guessedFieldFqcn));
        }
    }

    // transforms a generic Field class into a specific <type>Field class (e.g. DateTimeField)
    private function transformField(FieldDto $fieldDto, string $newFieldFqcn): FieldDto
    {
        /** @var FieldDto $newField */
        $newField = $newFieldFqcn::new($fieldDto->getProperty())->getAsDto();
        $newField->setUniqueId($fieldDto->getUniqueId());

        $newField->setFieldFqcn($newFieldFqcn);
        $newField->setDisplayedOn($fieldDto->getDisplayedOn());
        $newField->setValue($fieldDto->getValue());
        $newField->setFormattedValue($fieldDto->getFormattedValue());
        $newField->setCssClass(trim($newField->getCssClass().' '.$fieldDto->getCssClass()));
        $newField->setColumns($fieldDto->getColumns());
        $newField->setTranslationParameters($fieldDto->getTranslationParameters());
        $newField->setAssets($newField->getAssets()->mergeWith($fieldDto->getAssets()));
        foreach ($fieldDto->getFormThemes() as $formThemePath) {
            $newField->addFormTheme($formThemePath);
        }

        $customFormTypeOptions = $fieldDto->getFormTypeOptions();
        $defaultFormTypeOptions = $newField->getFormTypeOptions();
        $newField->setFormTypeOptions(array_merge($defaultFormTypeOptions, $customFormTypeOptions));

        $customFieldOptions = $fieldDto->getCustomOptions()->all();
        $defaultFieldOptions = $newField->getCustomOptions()->all();
        $mergedFieldOptions = array_merge($defaultFieldOptions, $customFieldOptions);
        $newField->setCustomOptions($mergedFieldOptions);

        if (null !== $fieldDto->getLabel()) {
            $newField->setLabel($fieldDto->getLabel());
        }

        if (null !== $fieldDto->isVirtual()) {
            $newField->setVirtual($fieldDto->isVirtual());
        }

        if (null !== $fieldDto->getTextAlign()) {
            $newField->setTextAlign($fieldDto->getTextAlign());
        }

        if (null !== $fieldDto->isSortable()) {
            $newField->setSortable($fieldDto->isSortable());
        }

        if (null !== $fieldDto->getPermission()) {
            $newField->setPermission($fieldDto->getPermission());
        }

        if (null !== $fieldDto->getHelp()) {
            $newField->setHelp($fieldDto->getHelp());
        }

        if (null !== $fieldDto->getFormType()) {
            $newField->setFormType($fieldDto->getFormType());
        }

        // don't copy the template name and path from the original Field class
        // (because they are just 'crud/field/text' and ' @EasyAdmin/crud/field/text.html.twig')
        // and use the template name/path from the new specific field (e.g. 'crud/field/datetime')

        return $newField;
    }
}

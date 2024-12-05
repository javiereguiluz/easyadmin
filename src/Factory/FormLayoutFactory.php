<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldLayoutDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnGroupCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnGroupOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabListType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneGroupCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneGroupOpenType;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Ulid;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @internal and @experimental don't use this in your own apps
 */
final class FormLayoutFactory
{
    public function __construct()
    {
    }

    public function createLayout(FieldCollection $fields, string $pageName): FieldCollection
    {
        // the index page has no layout elements such as tabs, form columns or fieldsets
        if (Crud::PAGE_INDEX === $pageName) {
            return $fields;
        }

        $this->validateLayoutConfiguration($fields);
        $this->addMissingLayoutFields($fields);
        $this->linearizeLayoutConfiguration($fields);

        return $fields;
    }

    private function validateLayoutConfiguration(FieldCollection $fields): void
    {
        // When rendering fields using tabs, all fields must belong to some tab.
        $firstRegularField = null;
        foreach ($fields as $fieldDto) {
            if ($fieldDto->isFormTab()) {
                if (null !== $firstRegularField) {
                    throw new \InvalidArgumentException(sprintf('When using form tabs, all fields must be rendered inside a tab. However, your field "%s" does not belong to any tab. Move it under a form tab or create a new form tab before it.', $firstRegularField->getProperty()));
                }

                // everything is fine; there are no regular fields before the first tab
                break;
            }

            if (!$fieldDto->isFormLayoutField()) {
                $firstRegularField = $fieldDto;
            }
        }

        // tabs can't be defined inside columns, but columns can be defined inside tabs:
        // Not allowed: ['column', 'tab', 'field', 'tab', 'field', ...]
        // Allowed:     ['tab', 'column', 'field', 'column', 'field', 'tab', ...]
        $theFirstFieldWhichIsATabOrColumn = null;
        foreach ($fields as $fieldDto) {
            if (!$fieldDto->isFormColumn() && !$fieldDto->isFormTab()) {
                continue;
            }

            if (null === $theFirstFieldWhichIsATabOrColumn) {
                $theFirstFieldWhichIsATabOrColumn = $fieldDto;
                continue;
            }

            if ($theFirstFieldWhichIsATabOrColumn->isFormColumn() && $fieldDto->isFormTab()) {
                throw new \InvalidArgumentException(sprintf('When using form columns, you can\'t define tabs inside columns (but you can define columns inside tabs). Move the tab "%s" outside any column.', $fieldDto->getLabel()));
            }
        }
    }

    /*
     * This is needed to handle edge-cases like this: the list of fields include one or more form fieldsets,
     * but the first fields of the list don't belong to any fieldset. Instead of throwing an error,
     * we create an automatic empty form fieldset for those "orphaned fields" so they are displayed as expected.
     */
    private function addMissingLayoutFields(FieldCollection $fields): void
    {
        $fieldThatMightBeOrphan = null;
        $weMightNeedToAddAFieldset = true;
        $insertFieldsetBeforeTheseFields = [];

        /** @var FieldDto $fieldDto */
        foreach ($fields as $fieldDto) {
            if ($fieldDto->isFormTab() || $fieldDto->isFormColumn()) {
                $weMightNeedToAddAFieldset = true;
                $fieldThatMightBeOrphan = null;
                continue;
            }

            if ($fieldDto->isFormFieldset()) {
                $weMightNeedToAddAFieldset = false;
            }

            if ($weMightNeedToAddAFieldset) {
                if ($fieldDto->isFormLayoutField()) {
                    continue;
                }

                if (null === $fieldThatMightBeOrphan) {
                    $fieldThatMightBeOrphan = $fieldDto;
                }
            }

            if ($fieldDto->isFormFieldset() && null !== $fieldThatMightBeOrphan) {
                $insertFieldsetBeforeTheseFields[] = $fieldThatMightBeOrphan;
                $fieldThatMightBeOrphan = null;
            }
        }

        foreach ($insertFieldsetBeforeTheseFields as $fieldDto) {
            $fields->insertBefore($this->createFieldsetOpenField(), $fieldDto);
        }
    }

    /*
     * This is used to add some special form types that will later simplify
     * the rendering of a form that uses columns, tabs, fieldsets, etc.
     *
     * For example, if the user configures this:
     *   FormField::addColumn()
     *   Field 1
     *   FormField::addColumn()
     *   Field 2
     *   Field 3
     * This method creates the following fields:
     *   FormField::openColumnGroup()
     *   FormField::openColumn()
     *   Field 1
     *   FormField::closeColumn()
     *   FormField::openColumn()
     *   Field 2
     *   Field 3
     *   FormField::closeColumn()
     *   FormField::closeColumnGroup()
     *
     * See tests for many other examples of complex layouts
     */
    private function linearizeLayoutConfiguration(FieldCollection $fields): void
    {
        $formUsesColumns = false;
        $formUsesTabs = false;
        $formUsesFieldsets = false;
        foreach ($fields as $fieldDto) {
            match (true) {
                $fieldDto->isFormColumn() => $formUsesColumns = true,
                $fieldDto->isFormTab() => $formUsesTabs = true,
                $fieldDto->isFormFieldset() => $formUsesFieldsets = true,
                default => null,
            };
        }

        $openedFormColumnDto = null;
        $openedFormColumnGroupDto = null;
        $openedFormTabDto = null;
        $openedFormFieldsetDto = null;
        $isFirstFormColumn = true;
        $tabsWithoutLabelCounter = 0;

        $slugger = new AsciiSlugger();
        $tabs = [];
        /** @var FieldDto $fieldDto */
        foreach ($fields as $fieldDto) {
            if ($formUsesColumns && !(null !== $openedFormColumnDto || null !== $openedFormTabDto) && !$fieldDto->isFormLayoutField()) {
                throw new \InvalidArgumentException(sprintf('When using form columns, all fields must be rendered inside a column. However, your field "%s" does not belong to any column. Move it under a form column or create a new form column before it.', $fieldDto->getProperty()));
            }

            if ($fieldDto->isFormTab()) {
                $isTabActive = 0 === \count($tabs);
                $tabId = sprintf('tab-%s', $fieldDto->getLabel() ? $slugger->slug(strip_tags($fieldDto->getLabel()))->lower()->toString() : ++$tabsWithoutLabelCounter);
                $fieldDto->setCustomOption(FormField::OPTION_TAB_ID, $tabId);
                $fieldDto->setCustomOption(FormField::OPTION_TAB_IS_ACTIVE, $isTabActive);

                $fieldDto->setFormTypeOptions([
                    'ea_tab_id' => $tabId,
                    'ea_css_class' => $fieldDto->getCssClass(),
                    'ea_help' => $fieldDto->getHelp(),
                ]);

                $tabs[$tabId] = $fieldDto;

                if (null !== $openedFormFieldsetDto) {
                    $fields->insertBefore($this->createFieldsetCloseField($openedFormFieldsetDto), $fieldDto);
                    $openedFormFieldsetDto = null;
                }

                if (null !== $openedFormColumnDto) {
                    $fields->insertBefore($this->createColumnCloseField($openedFormColumnDto), $fieldDto);
                    $fields->insertBefore($this->createColumnGroupCloseField($formUsesTabs, $openedFormColumnGroupDto), $fieldDto);
                    $openedFormColumnDto = null;
                    $openedFormColumnGroupDto = null;
                }

                if (null !== $openedFormTabDto) {
                    $fields->insertBefore($this->createTabPaneCloseField($openedFormTabDto), $fieldDto);
                }

                $openedFormTabDto = $fieldDto;
            }

            if ($fieldDto->isFormFieldset()) {
                if (null !== $openedFormFieldsetDto) {
                    $fields->insertBefore($this->createFieldsetCloseField($openedFormFieldsetDto), $fieldDto);
                }

                $openedFormFieldsetDto = $fieldDto;

                $fieldDto->setFormTypeOptions([
                    'ea_css_class' => $fieldDto->getCssClass(),
                    'ea_icon' => $fieldDto->getCustomOption('icon'),
                    'ea_help' => $fieldDto->getHelp(),
                    'ea_is_collapsible' => $fieldDto->getCustomOption(FormField::OPTION_COLLAPSIBLE),
                    'ea_is_collapsed' => $fieldDto->getCustomOption(FormField::OPTION_COLLAPSED),
                ]);
            }

            if ($fieldDto->isFormColumn()) {
                $formUsesColumns = true;

                if ($isFirstFormColumn) {
                    $fields->insertBefore($openedFormColumnGroupDto = $this->createColumnGroupOpenField($formUsesTabs), $fieldDto);
                    $isFirstFormColumn = false;
                }

                if (null !== $openedFormFieldsetDto) {
                    $fields->insertBefore($this->createFieldsetCloseField($openedFormFieldsetDto), $fieldDto);
                    $openedFormFieldsetDto = null;
                }

                if (null !== $openedFormColumnDto) {
                    $fields->insertBefore($this->createColumnCloseField($openedFormColumnDto), $fieldDto);
                }

                $openedFormColumnDto = $fieldDto;
            }

            if (null !== $openedFormColumnDto) {
                // this is needed because fields inside columns look better when they take the
                // entire width available; users can override this by setting custom CSS classes
                $fieldDto->setDefaultColumns('col-12');
            }
        }

        if (null !== $openedFormFieldsetDto) {
            $fields->add($this->createFieldsetCloseField($openedFormFieldsetDto));
        }

        if (null !== $openedFormColumnDto) {
            $fields->add($this->createColumnCloseField($openedFormColumnDto));
            $fields->add($this->createColumnGroupCloseField($formUsesTabs, $openedFormColumnGroupDto));
        }

        if ($formUsesTabs) {
            $fields->add($this->createTabPaneCloseField($openedFormTabDto));

            $fields->prepend($openedTabPaneGroupDto = $this->createTabPaneGroupOpenField());
            $fields->add($this->createTabPaneGroupCloseField($openedTabPaneGroupDto));

            $fields->prepend($this->createTabListField($tabs));
        }

        // if the form doesn't use any layout fields (tabs, columns, fieldsets, etc.),
        // wrap all fields inside a fieldset to simplify the rendering of the form later
        // (by default, this fieldset is invisible and doesn't change the form layout, so it's fine)
        if (!$formUsesTabs && !$formUsesColumns && !$formUsesFieldsets) {
            $fields->prepend($openedFormFieldsetDto = $this->createFieldsetOpenField());
            $fields->add($this->createFieldsetCloseField($openedFormFieldsetDto));
        }
    }

    private function createColumnGroupOpenField(bool $formUsesTabs): FieldDto
    {
        return Field::new('ea_form_column_group_open')
            ->setPropertySuffix(Ulid::generate())
            ->setFormType(EaFormColumnGroupOpenType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false, 'ea_is_inside_tab' => $formUsesTabs])
            ->getAsDto();
    }

    private function createColumnGroupCloseField(bool $formUsesTabs, ?FieldDto $openedDto): FieldDto
    {
        return Field::new('ea_form_column_group_close')
            ->setPropertySuffix($openedDto?->getPropertyNameSuffix() ?? Ulid::generate())
            ->setFormType(EaFormColumnGroupCloseType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false, 'ea_is_inside_tab' => $formUsesTabs])
            ->getAsDto();
    }

    private function createColumnCloseField(?FieldDto $openedDto): FieldDto
    {
        return Field::new('ea_form_column_close')
            ->setPropertySuffix($openedDto?->getPropertyNameSuffix() ?? Ulid::generate())
            ->setFormType(EaFormColumnCloseType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->getAsDto();
    }

    private function createFieldsetOpenField(): FieldDto
    {
        return FormField::addFieldset()
            ->setPropertySuffix(Ulid::generate())
            ->getAsDto();
    }

    private function createFieldsetCloseField(?FieldDto $openedDto): FieldDto
    {
        return Field::new('ea_form_fieldset_close')
            ->setPropertySuffix($openedDto?->getPropertyNameSuffix() ?? Ulid::generate())
            ->setFormType(EaFormFieldsetCloseType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->getAsDto();
    }

    private function createTabPaneGroupOpenField(): FieldDto
    {
        return Field::new('ea_form_tabpane_group_open')
            ->setPropertySuffix(Ulid::generate())
            ->setFormType(EaFormTabPaneGroupOpenType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->getAsDto();
    }

    private function createTabPaneGroupCloseField(?FieldDto $openedDto): FieldDto
    {
        return Field::new('ea_form_tabpane_group_close')
            ->setPropertySuffix($openedDto?->getPropertyNameSuffix() ?? Ulid::generate())
            ->setFormType(EaFormTabPaneGroupCloseType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->getAsDto();
    }

    private function createTabListField(array $tabs): FieldDto
    {
        return Field::new('ea_form_tablist')
            ->setPropertySuffix(Ulid::generate())
            ->setFormType(EaFormTabListType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->setCustomOption('tabs', $tabs)
            ->getAsDto();
    }

    private function createTabPaneCloseField(?FieldDto $openedDto): FieldDto
    {
        return Field::new('ea_form_tabpane_close')
            ->setPropertySuffix($openedDto?->getPropertyNameSuffix() ?? Ulid::generate())
            ->setFormType(EaFormTabPaneCloseType::class)
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->getAsDto();
    }

    public static function createFromFieldDtos(?FieldCollection $fieldDtos): FieldLayoutDto
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.8.0',
            '"FormLayoutFactory::createFromFieldDtos()" has been deprecated in favor of "FormLayoutFactory::createLayout()" and it will be removed in 5.0.0.',
        );

        if (null === $fieldDtos) {
            return new FieldLayoutDto();
        }

        $hasTabs = false;
        foreach ($fieldDtos as $fieldDto) {
            if ($fieldDto->isFormTab()) {
                $hasTabs = true;
                break;
            }
        }

        $tabs = [];
        $fields = [];
        $currentTab = null;
        /** @var FieldDto $fieldDto */
        foreach ($fieldDtos as $fieldDto) {
            if ($fieldDto->isFormTab()) {
                $currentTab = $fieldDto;
                $tabs[$fieldDto->getUniqueId()] = $fieldDto;
            } else {
                if ($hasTabs) {
                    $fields[$currentTab->getUniqueId()][] = $fieldDto;
                } else {
                    $fields[] = $fieldDto;
                }
            }
        }

        return new FieldLayoutDto($fields, $tabs);
    }
}

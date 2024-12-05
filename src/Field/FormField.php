<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EaFormRowType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneOpenType;
use Symfony\Component\Uid\Ulid;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FormField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ICON = 'icon';
    public const OPTION_COLLAPSIBLE = 'collapsible';
    public const OPTION_COLLAPSED = 'collapsed';
    public const OPTION_ROW_BREAKPOINT = 'rowBreakPoint';
    public const OPTION_TAB_ID = 'tabId';
    public const OPTION_TAB_IS_ACTIVE = 'tabIsActive';
    public const OPTION_TAB_ERROR_COUNT = 'tabErrorCount';

    /**
     * @internal Use the other named constructors instead (addPanel(), etc.)
     *
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null)
    {
        throw new \RuntimeException('Instead of this method, use the "addPanel()" method.');
    }

    /**
     * @param TranslatableInterface|string|false|null $label
     * @param string|null                             $icon  The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function addPanel($label = false, ?string $icon = null): self
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.7.7',
            '"FormField::addPanel()" has been deprecated in favor of "FormField::addFieldset()" and it will be removed in 5.0.0.',
        );

        return self::addFieldset($label, $icon);
    }

    /**
     * @param TranslatableInterface|string|false|null $label
     * @param string|null                             $icon  The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function addFieldset($label = false, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $field = new self();

        return $field
            ->setFieldFqcn(__CLASS__)
            ->hideOnIndex()
            ->setProperty('ea_form_fieldset')
            ->setPropertySuffix($propertySuffix ?? Ulid::generate())
            ->setLabel($label)
            ->setFormType(EaFormFieldsetOpenType::class)
            ->addCssClass('field-form_fieldset')
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->setCustomOption(self::OPTION_ICON, $icon)
            ->setCustomOption(self::OPTION_COLLAPSIBLE, false)
            ->setCustomOption(self::OPTION_COLLAPSED, false)
            ->setValue(true);
    }

    /**
     * @param string $breakpointName The name of the breakpoint where the new row is inserted
     *                               It must be a valid Bootstrap 5 name ('', 'sm', 'md', 'lg', 'xl', 'xxl')
     */
    public static function addRow(string $breakpointName = '', ?string $propertySuffix = null): self
    {
        $field = new self();

        $validBreakpointNames = ['', 'sm', 'md', 'lg', 'xl', 'xxl'];
        if (!\in_array($breakpointName, $validBreakpointNames, true)) {
            throw new \InvalidArgumentException(sprintf('The value passed to the "addRow()" method of "FormField" can only be one of these values: "%s" ("%s" was given).', implode(', ', $validBreakpointNames), $breakpointName));
        }

        return $field
            ->setFieldFqcn(__CLASS__)
            ->hideOnIndex()
            ->setProperty('ea_form_row')
            ->setPropertySuffix($propertySuffix ?? Ulid::generate())
            ->setFormType(EaFormRowType::class)
            ->addCssClass('field-form_row')
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->setCustomOption(self::OPTION_ROW_BREAKPOINT, $breakpointName)
            ->setValue(true);
    }

    /**
     * @return static
     */
    public static function addTab(TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $field = new self();

        return $field
            ->setFieldFqcn(__CLASS__)
            ->hideOnIndex()
            ->setProperty('ea_form_tab')
            ->setPropertySuffix($propertySuffix ?? Ulid::generate())
            ->setLabel($label)
            ->setFormType(EaFormTabPaneOpenType::class)
            ->addCssClass('field-form_tab')
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->setCustomOption(self::OPTION_ICON, $icon)
            ->setCustomOption(self::OPTION_TAB_ERROR_COUNT, 0)
            ->setValue(true);
    }

    /**
     * @param int|string $cols Any value compatible with Bootstrap grid system
     *                         (https://getbootstrap.com/docs/5.3/layout/grid/)
     *                         (e.g. 'col-6', 'col-sm-3', 'col-md-6 col-xl-4', etc.)
     *                         (integer values are transformed like this: N -> 'col-N')
     */
    public static function addColumn(int|string $cols = 'col', TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $help = null, ?string $propertySuffix = null): self
    {
        $field = new self();

        return $field
            ->setFieldFqcn(__CLASS__)
            ->hideOnIndex()
            ->setProperty('ea_form_column')
            ->setPropertySuffix($propertySuffix ?? Ulid::generate())
            ->setLabel($label)
            ->setFormType(EaFormColumnOpenType::class)
            ->addCssClass(sprintf('field-form_column %s', \is_int($cols) ? 'col-md-'.$cols : $cols))
            ->setFormTypeOptions(['mapped' => false, 'required' => false])
            ->setCustomOption(self::OPTION_ICON, $icon)
            ->setValue(true);
    }

    public function setIcon(string $iconCssClass): self
    {
        $this->setCustomOption(self::OPTION_ICON, $iconCssClass);

        return $this;
    }

    public function collapsible(bool $collapsible = true): self
    {
        if (!$this->hasLabelOrIcon()) {
            throw new \InvalidArgumentException(sprintf('The %s() method used in one of your fieldsets requires that the fieldset defines either a label or an icon, but it defines none of them.', __METHOD__));
        }

        $this->setCustomOption(self::OPTION_COLLAPSIBLE, $collapsible);

        return $this;
    }

    public function renderCollapsed(bool $collapsed = true): self
    {
        if (!$this->hasLabelOrIcon()) {
            throw new \InvalidArgumentException(sprintf('The %s() method used in one of your fieldsets requires that the fieldset defines either a label or an icon, but it defines none of them.', __METHOD__));
        }

        $this->setCustomOption(self::OPTION_COLLAPSIBLE, true);
        $this->setCustomOption(self::OPTION_COLLAPSED, $collapsed);

        return $this;
    }

    private function hasLabelOrIcon(): bool
    {
        // don't use empty() because the label can contain only white spaces (it's a valid edge-case)
        return (null !== $this->dto->getLabel() && '' !== $this->dto->getLabel())
            || null !== $this->dto->getCustomOption(self::OPTION_ICON);
    }
}

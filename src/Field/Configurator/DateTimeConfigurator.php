<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Intl\IntlFormatterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class DateTimeConfigurator implements FieldConfiguratorInterface
{
    private IntlFormatterInterface $intlFormatter;

    public function __construct(IntlFormatterInterface $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return \in_array($field->getFieldFqcn(), [DateTimeField::class, DateField::class, TimeField::class], true);
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        // we don't require this PHP extension in composer.json because it's not mandatory to display
        // date/time fields in backends, so this is not a hard dependency
        if (!\extension_loaded('intl')) {
            throw new \LogicException('When using date/time fields in EasyAdmin backends, you must install and enable the PHP Intl extension, which is used to format date/time values.');
        }

        $crud = $context->getCrud();

        $defaultTimezone = $crud->getTimezone();
        $timezone = $field->getCustomOption(DateTimeField::OPTION_TIMEZONE) ?? $defaultTimezone;

        $dateFormat = null;
        $timeFormat = null;
        $icuDateTimePattern = '';
        $formattedValue = $field->getValue();

        if (DateTimeField::class === $field->getFieldFqcn()) {
            [$defaultDatePattern, $defaultTimePattern] = $crud->getDateTimePattern();
            $datePattern = $field->getCustomOption(DateTimeField::OPTION_DATE_PATTERN) ?? $defaultDatePattern;
            $timePattern = $field->getCustomOption(DateTimeField::OPTION_TIME_PATTERN) ?? $defaultTimePattern;
            if (\in_array($datePattern, DateTimeField::VALID_DATE_FORMATS, true)) {
                $dateFormat = $datePattern;
                $timeFormat = $timePattern;
            } else {
                $icuDateTimePattern = $datePattern;
            }

            $formattedValue = $this->intlFormatter->formatDateTime($field->getValue(), $dateFormat, $timeFormat, $icuDateTimePattern, $timezone);
        } elseif (DateField::class === $field->getFieldFqcn()) {
            $dateFormatOrPattern = $field->getCustomOption(DateField::OPTION_DATE_PATTERN) ?? $crud->getDatePattern();
            if (\in_array($dateFormatOrPattern, DateTimeField::VALID_DATE_FORMATS, true)) {
                $dateFormat = $dateFormatOrPattern;
            } else {
                $icuDateTimePattern = $dateFormatOrPattern;
            }

            $formattedValue = $this->intlFormatter->formatDate($field->getValue(), $dateFormat, $icuDateTimePattern, $timezone);
        } elseif (TimeField::class === $field->getFieldFqcn()) {
            $timeFormatOrPattern = $field->getCustomOption(TimeField::OPTION_TIME_PATTERN) ?? $crud->getTimePattern();
            if (\in_array($timeFormatOrPattern, DateTimeField::VALID_DATE_FORMATS, true)) {
                $timeFormat = $timeFormatOrPattern;
            } else {
                $icuDateTimePattern = $timeFormatOrPattern;
            }

            $formattedValue = $this->intlFormatter->formatTime($field->getValue(), $timeFormat, $icuDateTimePattern, $timezone);
        }

        $widgetOption = $field->getCustomOption(DateTimeField::OPTION_WIDGET);
        if (DateTimeField::WIDGET_NATIVE === $widgetOption) {
            $field->setFormTypeOption('widget', 'single_text');
            $field->setFormTypeOption('html5', true);
        } elseif (DateTimeField::WIDGET_CHOICE === $widgetOption) {
            $field->setFormTypeOption('widget', 'choice');
            $field->setFormTypeOption('html5', true);
        } elseif (DateTimeField::WIDGET_TEXT === $widgetOption) {
            $field->setFormTypeOption('widget', 'single_text');
            $field->setFormTypeOption('html5', false);
        }

        $field->setFormattedValue($formattedValue);

        // check if the property is immutable, but only if it's a real Doctrine entity property
        if (!$entityDto->hasProperty($field->getProperty())) {
            return;
        }
        $doctrineDataType = $entityDto->getPropertyMetadata($field->getProperty())->get('type');
        $isImmutableDateTime = \in_array($doctrineDataType, [Types::DATETIMETZ_IMMUTABLE, Types::DATETIME_IMMUTABLE, Types::DATE_IMMUTABLE, Types::TIME_IMMUTABLE], true);
        if ($isImmutableDateTime) {
            $field->setFormTypeOptionIfNotSet('input', 'datetime_immutable');
        }
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class DateTimeField implements FieldInterface
{
    use FieldTrait;

    public const FORMAT_FULL = 'full';
    public const FORMAT_LONG = 'long';
    public const FORMAT_MEDIUM = 'medium';
    public const FORMAT_SHORT = 'short';
    public const FORMAT_NONE = 'none';

    public const VALID_DATE_FORMATS = [self::FORMAT_NONE, self::FORMAT_SHORT, self::FORMAT_MEDIUM, self::FORMAT_LONG, self::FORMAT_FULL];

    public const WIDGET_NATIVE = 'native';
    public const WIDGET_CHOICE = 'choice';
    public const WIDGET_TEXT = 'text';

    public const OPTION_DATE_PATTERN = 'datePattern';
    public const OPTION_TIME_PATTERN = 'timePattern';
    public const OPTION_TIMEZONE = 'timezone';
    public const OPTION_WIDGET = 'widget';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/datetime')
            ->setFormType(DateTimeType::class)
            ->addCssClass('field-datetime')
            ->setDefaultColumns('col-md-6 col-xxl-5')
            // the proper default values of these options are set on the Crud class
            ->setCustomOption(self::OPTION_DATE_PATTERN, null)
            ->setCustomOption(self::OPTION_TIME_PATTERN, null)
            ->setCustomOption(self::OPTION_TIMEZONE, null)
            ->setCustomOption(self::OPTION_WIDGET, self::WIDGET_NATIVE);
    }

    /**
     * @param string $timezoneId A valid PHP timezone ID
     */
    public function setTimezone(string $timezoneId): self
    {
        if (!\in_array($timezoneId, timezone_identifiers_list(), true)) {
            throw new \InvalidArgumentException(sprintf('The "%s" timezone is not a valid PHP timezone ID. Use any of the values listed at https://www.php.net/manual/en/timezones.php', $timezoneId));
        }

        $this->setCustomOption(self::OPTION_TIMEZONE, $timezoneId);

        return $this;
    }

    /**
     * @param string $dateFormatOrPattern A format name ('none', 'short', 'medium', 'long', 'full') or a valid ICU Datetime Pattern (see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#formatting-dates)
     * @param string $timeFormat          A format name ('none', 'short', 'medium', 'long', 'full')
     */
    public function setFormat(string $dateFormatOrPattern, string $timeFormat = self::FORMAT_NONE): self
    {
        if ('' === trim($dateFormatOrPattern)) {
            throw new \InvalidArgumentException(sprintf('The first argument of the "%s()" method cannot be an empty string. Use either a date format (%s) or a datetime Intl pattern.', __METHOD__, implode(', ', self::VALID_DATE_FORMATS)));
        }

        $datePatternIsEmpty = self::FORMAT_NONE === $dateFormatOrPattern;
        $timePatternIsEmpty = self::FORMAT_NONE === $timeFormat || '' === trim($timeFormat);
        if ($datePatternIsEmpty && $timePatternIsEmpty) {
            throw new \InvalidArgumentException(sprintf('The values of the arguments of "%s()" cannot be "%s" or an empty string at the same time. Change any of them (or both).', __METHOD__, self::FORMAT_NONE));
        }

        // when date format/pattern is none and time format is a pattern,
        // silently turn them into a datetime pattern
        if (self::FORMAT_NONE === $dateFormatOrPattern && !\in_array($timeFormat, self::VALID_DATE_FORMATS, true)) {
            $dateFormatOrPattern = $timeFormat;
            $timeFormat = self::FORMAT_NONE;
        }

        $isDatePattern = !\in_array($dateFormatOrPattern, self::VALID_DATE_FORMATS, true);

        if ($isDatePattern && self::FORMAT_NONE !== $timeFormat) {
            throw new \InvalidArgumentException(sprintf('When the first argument of "%s()" is a datetime pattern, you cannot set the time format in the second argument (define the time format inside the datetime pattern).', __METHOD__));
        }

        if (!$isDatePattern && !\in_array($timeFormat, self::VALID_DATE_FORMATS, true)) {
            throw new \InvalidArgumentException(sprintf('The value of the time format can only be one of the following: %s (but "%s" was given).', implode(', ', self::VALID_DATE_FORMATS), $timeFormat));
        }

        $this->setCustomOption(self::OPTION_DATE_PATTERN, $dateFormatOrPattern);
        $this->setCustomOption(self::OPTION_TIME_PATTERN, $timeFormat);

        return $this;
    }

    /**
     * Uses native HTML5 widgets when rendering this field in forms.
     */
    public function renderAsNativeWidget(bool $asNative = true): self
    {
        if (false === $asNative) {
            $this->renderAsChoice();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_NATIVE);
        }

        return $this;
    }

    /**
     * Uses <select> lists when rendering this field in forms.
     */
    public function renderAsChoice(bool $asChoice = true): self
    {
        if (false === $asChoice) {
            $this->renderAsNativeWidget();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_CHOICE);
        }

        return $this;
    }

    /**
     * Uses <input type="text"> elements when rendering this field in forms.
     */
    public function renderAsText(bool $asText = true): self
    {
        if (false === $asText) {
            $this->renderAsNativeWidget();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_TEXT);
        }

        return $this;
    }
}

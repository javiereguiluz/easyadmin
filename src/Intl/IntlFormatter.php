<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Intl;

use Twig\Error\RuntimeError;

/**
 * Copied from https://github.com/twigphp/intl-extra/blob/2.x/src/IntlExtension.php
 * (c) Fabien Potencier - MIT License.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class IntlFormatter
{
    private const DATE_FORMATS = [
        'none' => \IntlDateFormatter::NONE,
        'short' => \IntlDateFormatter::SHORT,
        'medium' => \IntlDateFormatter::MEDIUM,
        'long' => \IntlDateFormatter::LONG,
        'full' => \IntlDateFormatter::FULL,
    ];
    private const NUMBER_TYPES = [
        'default' => \NumberFormatter::TYPE_DEFAULT,
        'int32' => \NumberFormatter::TYPE_INT32,
        'int64' => \NumberFormatter::TYPE_INT64,
        'double' => \NumberFormatter::TYPE_DOUBLE,
    ];
    private const NUMBER_STYLES = [
        'decimal' => \NumberFormatter::DECIMAL,
        'currency' => \NumberFormatter::CURRENCY,
        'percent' => \NumberFormatter::PERCENT,
        'scientific' => \NumberFormatter::SCIENTIFIC,
        'spellout' => \NumberFormatter::SPELLOUT,
        'ordinal' => \NumberFormatter::ORDINAL,
        'duration' => \NumberFormatter::DURATION,
    ];
    private const NUMBER_ATTRIBUTES = [
        'grouping_used' => \NumberFormatter::GROUPING_USED,
        'decimal_always_shown' => \NumberFormatter::DECIMAL_ALWAYS_SHOWN,
        'max_integer_digit' => \NumberFormatter::MAX_INTEGER_DIGITS,
        'min_integer_digit' => \NumberFormatter::MIN_INTEGER_DIGITS,
        'integer_digit' => \NumberFormatter::INTEGER_DIGITS,
        'max_fraction_digit' => \NumberFormatter::MAX_FRACTION_DIGITS,
        'min_fraction_digit' => \NumberFormatter::MIN_FRACTION_DIGITS,
        'fraction_digit' => \NumberFormatter::FRACTION_DIGITS,
        'multiplier' => \NumberFormatter::MULTIPLIER,
        'grouping_size' => \NumberFormatter::GROUPING_SIZE,
        'rounding_mode' => \NumberFormatter::ROUNDING_MODE,
        'rounding_increment' => \NumberFormatter::ROUNDING_INCREMENT,
        'format_width' => \NumberFormatter::FORMAT_WIDTH,
        'padding_position' => \NumberFormatter::PADDING_POSITION,
        'secondary_grouping_size' => \NumberFormatter::SECONDARY_GROUPING_SIZE,
        'significant_digits_used' => \NumberFormatter::SIGNIFICANT_DIGITS_USED,
        'min_significant_digits_used' => \NumberFormatter::MIN_SIGNIFICANT_DIGITS,
        'max_significant_digits_used' => \NumberFormatter::MAX_SIGNIFICANT_DIGITS,
        'lenient_parse' => \NumberFormatter::LENIENT_PARSE,
    ];
    private const NUMBER_ROUNDING_ATTRIBUTES = [
        'ceiling' => \NumberFormatter::ROUND_CEILING,
        'floor' => \NumberFormatter::ROUND_FLOOR,
        'down' => \NumberFormatter::ROUND_DOWN,
        'up' => \NumberFormatter::ROUND_UP,
        'halfeven' => \NumberFormatter::ROUND_HALFEVEN,
        'halfdown' => \NumberFormatter::ROUND_HALFDOWN,
        'halfup' => \NumberFormatter::ROUND_HALFUP,
    ];
    private const NUMBER_PADDING_ATTRIBUTES = [
        'before_prefix' => \NumberFormatter::PAD_BEFORE_PREFIX,
        'after_prefix' => \NumberFormatter::PAD_AFTER_PREFIX,
        'before_suffix' => \NumberFormatter::PAD_BEFORE_SUFFIX,
        'after_suffix' => \NumberFormatter::PAD_AFTER_SUFFIX,
    ];
    private const NUMBER_TEXT_ATTRIBUTES = [
        'positive_prefix' => \NumberFormatter::POSITIVE_PREFIX,
        'positive_suffix' => \NumberFormatter::POSITIVE_SUFFIX,
        'negative_prefix' => \NumberFormatter::NEGATIVE_PREFIX,
        'negative_suffix' => \NumberFormatter::NEGATIVE_SUFFIX,
        'padding_character' => \NumberFormatter::PADDING_CHARACTER,
        'currency_code' => \NumberFormatter::CURRENCY_CODE,
        'default_ruleset' => \NumberFormatter::DEFAULT_RULESET,
        'public_rulesets' => \NumberFormatter::PUBLIC_RULESETS,
    ];
    private const NUMBER_SYMBOLS = [
        'decimal_separator' => \NumberFormatter::DECIMAL_SEPARATOR_SYMBOL,
        'grouping_separator' => \NumberFormatter::GROUPING_SEPARATOR_SYMBOL,
        'pattern_separator' => \NumberFormatter::PATTERN_SEPARATOR_SYMBOL,
        'percent' => \NumberFormatter::PERCENT_SYMBOL,
        'zero_digit' => \NumberFormatter::ZERO_DIGIT_SYMBOL,
        'digit' => \NumberFormatter::DIGIT_SYMBOL,
        'minus_sign' => \NumberFormatter::MINUS_SIGN_SYMBOL,
        'plus_sign' => \NumberFormatter::PLUS_SIGN_SYMBOL,
        'currency' => \NumberFormatter::CURRENCY_SYMBOL,
        'intl_currency' => \NumberFormatter::INTL_CURRENCY_SYMBOL,
        'monetary_separator' => \NumberFormatter::MONETARY_SEPARATOR_SYMBOL,
        'exponential' => \NumberFormatter::EXPONENTIAL_SYMBOL,
        'permill' => \NumberFormatter::PERMILL_SYMBOL,
        'pad_escape' => \NumberFormatter::PAD_ESCAPE_SYMBOL,
        'infinity' => \NumberFormatter::INFINITY_SYMBOL,
        'nan' => \NumberFormatter::NAN_SYMBOL,
        'significant_digit' => \NumberFormatter::SIGNIFICANT_DIGIT_SYMBOL,
        'monetary_grouping_separator' => \NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL,
    ];

    private array $dateFormatters = [];
    private array $numberFormatters = [];

    public function formatCurrency($amount, string $currency, array $attrs = [], ?string $locale = null): string
    {
        $formatter = $this->createNumberFormatter($locale, 'currency', $attrs);
        /** @var string|false $formattedCurrency */
        $formattedCurrency = $formatter->formatCurrency($amount, $currency);
        if (false === $formattedCurrency) {
            throw new RuntimeError('Unable to format the given number as a currency.');
        }

        return $formattedCurrency;
    }

    public function formatNumber(int|float $number, array $attrs = [], string $style = 'decimal', string $type = 'default', ?string $locale = null): string
    {
        if (!isset(self::NUMBER_TYPES[$type])) {
            throw new RuntimeError(sprintf('The type "%s" does not exist, known types are: "%s".', $type, implode('", "', array_keys(self::NUMBER_TYPES))));
        }

        $formatter = $this->createNumberFormatter($locale, $style, $attrs);

        if (false === $ret = $formatter->format($number, self::NUMBER_TYPES[$type])) {
            throw new RuntimeError('Unable to format the given number.');
        }

        return $ret;
    }

    /**
     * @param \DateTimeZone|string|false|null $timezone The target timezone, null to use the default, false to leave unchanged
     */
    public function formatDateTime(?\DateTimeInterface $date, ?string $dateFormat = 'medium', ?string $timeFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string
    {
        if (null === $date = $this->convertDate($date, $timezone)) {
            return null;
        }

        $formatter = $this->createDateFormatter($locale, $dateFormat, $timeFormat, $pattern, $date->getTimezone(), $calendar);
        $formattedDateTime = $formatter->format($date);

        return false !== $formattedDateTime ? $formattedDateTime : null;
    }

    /**
     * @param \DateTimeZone|string|false|null $timezone The target timezone, null to use the default, false to leave unchanged
     */
    public function formatDate(?\DateTimeInterface $date, ?string $dateFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string
    {
        return $this->formatDateTime($date, $dateFormat, 'none', $pattern, $timezone, $calendar, $locale);
    }

    /**
     * @param \DateTimeZone|string|false|null $timezone The target timezone, null to use the default, false to leave unchanged
     */
    public function formatTime(?\DateTimeInterface $date, ?string $timeFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string
    {
        return $this->formatDateTime($date, 'none', $timeFormat, $pattern, $timezone, $calendar, $locale);
    }

    private function createDateFormatter(?string $locale, ?string $dateFormat, ?string $timeFormat, string $pattern = '', ?\DateTimeZone $timezone = null, string $calendarName = 'gregorian'): \IntlDateFormatter
    {
        if (null !== $dateFormat && !isset(self::DATE_FORMATS[$dateFormat])) {
            throw new RuntimeError(sprintf('The date format "%s" does not exist, known formats are: "%s".', $dateFormat, implode('", "', array_keys(self::DATE_FORMATS))));
        }

        if (null !== $timeFormat && !isset(self::DATE_FORMATS[$timeFormat])) {
            throw new RuntimeError(sprintf('The time format "%s" does not exist, known formats are: "%s".', $timeFormat, implode('", "', array_keys(self::DATE_FORMATS))));
        }

        if (null === $locale) {
            $locale = \Locale::getDefault();
        }

        $calendar = 'gregorian' === $calendarName ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL;

        $dateFormatValue = self::DATE_FORMATS[$dateFormat] ?? self::DATE_FORMATS['full'];
        $timeFormatValue = self::DATE_FORMATS[$timeFormat] ?? self::DATE_FORMATS['full'];

        $hash = $locale.'|'.$dateFormatValue.'|'.$timeFormatValue.'|'.$timezone->getName().'|'.$calendar.'|'.$pattern;

        if (!isset($this->dateFormatters[$hash])) {
            $this->dateFormatters[$hash] = new \IntlDateFormatter($locale, $dateFormatValue, $timeFormatValue, $timezone, $calendar, $pattern);
        }

        return $this->dateFormatters[$hash];
    }

    private function createNumberFormatter(?string $locale, string $style, array $attrs = []): \NumberFormatter
    {
        if (!isset(self::NUMBER_STYLES[$style])) {
            throw new RuntimeError(sprintf('The style "%s" does not exist, known styles are: "%s".', $style, implode('", "', array_keys(self::NUMBER_STYLES))));
        }

        if (null === $locale) {
            $locale = \Locale::getDefault();
        }

        $textAttrs = [];
        foreach ($attrs as $name => $value) {
            if (isset(self::NUMBER_TEXT_ATTRIBUTES[$name])) {
                $textAttrs[$name] = $value;
                unset($attrs[$name]);
            }
        }

        $symbols = [];
        foreach ($attrs as $name => $value) {
            if (isset(self::NUMBER_SYMBOLS[$name])) {
                $symbols[$name] = $value;
                unset($attrs[$name]);
            }
        }

        ksort($attrs);
        $hash = $locale.'|'.$style.'|'.json_encode($attrs).'|'.json_encode($textAttrs).'|'.json_encode($symbols);

        if (!isset($this->numberFormatters[$hash])) {
            $this->numberFormatters[$hash] = new \NumberFormatter($locale, self::NUMBER_STYLES[$style]);
        }

        foreach ($attrs as $name => $value) {
            if (!isset(self::NUMBER_ATTRIBUTES[$name])) {
                throw new RuntimeError(sprintf('The number formatter attribute "%s" does not exist, known attributes are: "%s".', $name, implode('", "', array_keys(self::NUMBER_ATTRIBUTES))));
            }

            if ('rounding_mode' === $name) {
                if (!isset(self::NUMBER_ROUNDING_ATTRIBUTES[$value])) {
                    throw new RuntimeError(sprintf('The number formatter rounding mode "%s" does not exist, known modes are: "%s".', $value, implode('", "', array_keys(self::NUMBER_ROUNDING_ATTRIBUTES))));
                }

                $value = self::NUMBER_ROUNDING_ATTRIBUTES[$value];
            } elseif ('padding_position' === $name) {
                if (!isset(self::NUMBER_PADDING_ATTRIBUTES[$value])) {
                    throw new RuntimeError(sprintf('The number formatter padding position "%s" does not exist, known positions are: "%s".', $value, implode('", "', array_keys(self::NUMBER_PADDING_ATTRIBUTES))));
                }

                $value = self::NUMBER_PADDING_ATTRIBUTES[$value];
            }

            $this->numberFormatters[$hash]->setAttribute(self::NUMBER_ATTRIBUTES[$name], $value);
        }

        foreach ($textAttrs as $name => $value) {
            $this->numberFormatters[$hash]->setTextAttribute(self::NUMBER_TEXT_ATTRIBUTES[$name], $value);
        }

        foreach ($symbols as $name => $value) {
            $this->numberFormatters[$hash]->setSymbol(self::NUMBER_SYMBOLS[$name], $value);
        }

        return $this->numberFormatters[$hash];
    }

    private function convertDate(?\DateTimeInterface $date, $timezone = null): ?\DateTimeInterface
    {
        if (null === $date) {
            return null;
        }

        if (null === $timezone) {
            $timezone = new \DateTimeZone(date_default_timezone_get());
        } elseif (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }

        if ($date instanceof \DateTimeImmutable) {
            return $date->setTimezone($timezone);
        }

        $date = clone $date;
        $date->setTimezone($timezone);

        return $date;
    }
}

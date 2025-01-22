<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Intl;

interface IntlFormatterInterface
{
    public function formatCurrency(int|float $amount, string $currency, array $attrs = [], ?string $locale = null): string;

    public function formatNumber(int|float $number, array $attrs = [], string $style = 'decimal', string $type = 'default', ?string $locale = null): string;

    /**
     * @param \DateTimeZone|string|bool|null $timezone
     */
    public function formatDateTime(?\DateTimeInterface $date, ?string $dateFormat = 'medium', ?string $timeFormat = 'medium', string $pattern = '', /* \DateTimeZone|string|bool|null */ $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string;

    /**
     * @param \DateTimeZone|string|bool|null $timezone
     */
    public function formatDate(?\DateTimeInterface $date, ?string $dateFormat = 'medium', string $pattern = '', /* \DateTimeZone|string|bool|null */ $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string;

    /**
     * @param \DateTimeZone|string|bool|null $timezone
     */
    public function formatTime(?\DateTimeInterface $date, ?string $timeFormat = 'medium', string $pattern = '', /* \DateTimeZone|string|bool|null */ $timezone = null, string $calendar = 'gregorian', ?string $locale = null): ?string;
}

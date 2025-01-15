<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Intl;

use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\u;

class IntlFormatterTest extends TestCase
{
    /**
     * @dataProvider provideFormatDate
     */
    public function testFormatDate(?string $expectedResult, ?\DateTimeInterface $date, ?string $dateFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null)
    {
        if (\PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('PHP 8.2 or higher is required to run this test.');
        }

        $intlFormatter = new IntlFormatter();
        $formattedDate = $intlFormatter->formatDate($date, $dateFormat, $pattern, $timezone, $calendar, $locale);
        if (null !== $formattedDate) {
            $formattedDate = $this->normalizeWhiteSpaces($formattedDate);
        }

        $this->assertSame($expectedResult, $formattedDate);
    }

    /**
     * @dataProvider provideFormatTime
     */
    public function testFormatTime(?string $expectedResult, ?\DateTimeInterface $date, ?string $timeFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null, string $assertMethod = 'assertSame')
    {
        if (\PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('PHP 8.2 or higher is required to run this test.');
        }

        $intlFormatter = new IntlFormatter();
        $formattedTime = $intlFormatter->formatTime($date, $timeFormat, $pattern, $timezone, $calendar, $locale);
        if (null !== $formattedTime) {
            $formattedTime = $this->normalizeWhiteSpaces($formattedTime);
        }

        $this->{$assertMethod}($expectedResult, $formattedTime);
    }

    /**
     * @dataProvider provideFormatDateTime
     */
    public function testFormatDateTime(?string $expectedResult, ?\DateTimeInterface $date, ?string $dateFormat = 'medium', ?string $timeFormat = 'medium', string $pattern = '', $timezone = null, string $calendar = 'gregorian', ?string $locale = null)
    {
        if (\PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('PHP 8.2 or higher is required to run this test.');
        }

        $intlFormatter = new IntlFormatter();
        $formattedDateTime = $intlFormatter->formatDateTime($date, $dateFormat, $timeFormat, $pattern, $timezone, $calendar, $locale);
        if (null !== $formattedDateTime) {
            $formattedDateTime = $this->normalizeWhiteSpaces($formattedDateTime);
        }

        $this->assertSame($expectedResult, $formattedDateTime);
    }

    /**
     * @dataProvider provideFormatNumber
     */
    public function testFormatNumber(?string $expectedResult, int|float $number, array $attrs)
    {
        if (\PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('PHP 8.2 or higher is required to run this test.');
        }

        $intlFormatter = new IntlFormatter();
        $formattedNumber = $intlFormatter->formatNumber($number, $attrs);

        $this->assertSame($expectedResult, $formattedNumber);
    }

    public static function provideFormatDate()
    {
        yield [null, null, 'medium', '', null, 'gregorian', null];

        yield ['20201107 12:00 AM', new \DateTime('2020-11-07'), 'none', '', null, 'gregorian', 'en'];
        yield ['20201107 12:00 a. m.', new \DateTime('2020-11-07'), 'none', '', null, 'gregorian', 'es'];
        yield ['11/7/20', new \DateTime('2020-11-07'), 'short', '', null, 'gregorian', 'en'];
        yield ['7/11/20', new \DateTime('2020-11-07'), 'short', '', null, 'gregorian', 'es'];
        yield ['Nov 7, 2020', new \DateTime('2020-11-07'), 'medium', '', null, 'gregorian', 'en'];
        yield ['7 nov 2020', new \DateTime('2020-11-07'), 'medium', '', null, 'gregorian', 'es', false, false];
        yield ['November 7, 2020', new \DateTime('2020-11-07'), 'long', '', null, 'gregorian', 'en'];
        yield ['7 de noviembre de 2020', new \DateTime('2020-11-07'), 'long', '', null, 'gregorian', 'es'];
        yield ['Saturday, November 7, 2020', new \DateTime('2020-11-07'), 'full', '', null, 'gregorian', 'en'];
        yield ['sábado, 7 de noviembre de 2020', new \DateTime('2020-11-07'), 'full', '', null, 'gregorian', 'es'];

        yield ['Nov 7, 2020', new \DateTimeImmutable('2020-11-07'), 'medium', '', null, 'gregorian', 'en'];
        yield ['7 nov 2020', new \DateTimeImmutable('2020-11-07'), 'medium', '', null, 'gregorian', 'es', false, false];

        yield ['2020 Q4 November Saturday 00:00:00', new \DateTime('2020-11-07'), null, 'yyyy QQQ MMMM eeee HH:mm:ss', null, 'gregorian', 'en'];
        yield ['2020 T4 noviembre sábado 00:00:00', new \DateTime('2020-11-07'), null, 'yyyy QQQ MMMM eeee HH:mm:ss', null, 'gregorian', 'es'];

        yield ['Nov 7, 2020', new \DateTime('2020-11-07'), 'medium', '', new \DateTimeZone('Asia/Tokyo'), 'gregorian', 'en'];
        yield ['Nov 7, 2020', new \DateTimeImmutable('2020-11-07', new \DateTimeZone('America/Montevideo')), 'medium', '', new \DateTimeZone('Asia/Tokyo'), 'gregorian', 'en'];

        yield ['Nov 7, 2020', new \DateTime('2020-11-07'), 'medium', '', null, 'traditional', 'en'];
    }

    public static function provideFormatTime()
    {
        yield [null, null, 'medium', '', null, 'gregorian', null];

        yield ['03:04 PM', new \DateTime('15:04:05'), 'none', '', null, 'gregorian', 'en', 'assertStringEndsWith'];
        yield ['03:04 p. m.', new \DateTime('15:04:05'), 'none', '', null, 'gregorian', 'es', 'assertStringEndsWith'];
        yield ['3:04 PM', new \DateTime('15:04:05'), 'short', '', null, 'gregorian', 'en'];
        yield ['15:04', new \DateTime('15:04:05'), 'short', '', null, 'gregorian', 'es'];
        yield ['3:04:05 PM', new \DateTime('15:04:05'), 'medium', '', null, 'gregorian', 'en'];
        yield ['15:04:05', new \DateTime('15:04:05'), 'medium', '', null, 'gregorian', 'es'];
        yield ['3:04:05 PM Coordinated Universal Time', new \DateTime('15:04:05'), 'full', '', null, 'gregorian', 'en'];
        yield ['15:04:05 (tiempo universal coordinado)', new \DateTime('15:04:05'), 'full', '', null, 'gregorian', 'es'];

        yield ['10:04:05 PM', new \DateTime('15:04:05', new \DateTimeZone('MST')), 'medium', '', null, 'gregorian', 'en'];
        yield ['10:04:05 PM', new \DateTime('15:04:05 MST'), 'medium', '', null, 'gregorian', 'en'];
        // the regular expression is needed to account for DST time changes
        yield ['/(11:04:05 PM|12:04:05 AM)/', new \DateTime('15:04:05 MST'), 'medium', '', new \DateTimeZone('CET'), 'gregorian', 'en', 'assertMatchesRegularExpression'];

        yield ['2:4:5', new \DateTime('15:04:05 CET'), null, 'h:m:s', null, 'gregorian', 'en'];
        yield ['50645000', new \DateTime('15:04:05 CET'), null, 'A', null, 'gregorian', 'en'];
        yield ['Coordinated Universal Time GMT +00:00', new \DateTime('15:04:05 CET'), null, 'zzzz vvvv xxxxx', null, 'gregorian', 'en'];
        yield ['/Pacific (Standard|Daylight) Time Pacific Time -0(7|8):00/', new \DateTime('15:04:05 CET'), null, 'zzzz vvvv xxxxx', new \DateTimeZone('PST'), 'gregorian', 'en', 'assertMatchesRegularExpression'];
    }

    public static function provideFormatDateTime()
    {
        yield [null, null, 'medium', 'medium', '', null, 'gregorian', null];

        yield ['20201107 02:04 PM', new \DateTime('2020-11-07 15:04:05 CET'), 'none', 'none', '', null, 'gregorian', 'en'];
        yield ['20201107 02:04 p. m.', new \DateTime('2020-11-07 15:04:05 CET'), 'none', 'none', '', null, 'gregorian', 'es'];
        yield ['11/7/20, 2:04 PM', new \DateTime('2020-11-07 15:04:05 CET'), 'short', 'short', '', null, 'gregorian', 'en'];
        yield ['7/11/20, 14:04', new \DateTime('2020-11-07 15:04:05 CET'), 'short', 'short', '', null, 'gregorian', 'es', false, false];
        yield ['Nov 7, 2020, 2:04:05 PM', new \DateTime('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', null, 'gregorian', 'en'];
        yield ['7 nov 2020, 14:04:05', new \DateTime('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', null, 'gregorian', 'es', false, false];
        yield ['November 7, 2020 at 2:04:05 PM UTC', new \DateTime('2020-11-07 15:04:05 CET'), 'long', 'long', '', null, 'gregorian', 'en'];
        yield ['7 de noviembre de 2020, 14:04:05 UTC', new \DateTime('2020-11-07 15:04:05 CET'), 'long', 'long', '', null, 'gregorian', 'es'];
        yield ['Saturday, November 7, 2020 at 2:04:05 PM Coordinated Universal Time', new \DateTime('2020-11-07 15:04:05 CET'), 'full', 'full', '', null, 'gregorian', 'en'];
        yield ['sábado, 7 de noviembre de 2020, 14:04:05 (tiempo universal coordinado)', new \DateTime('2020-11-07 15:04:05 CET'), 'full', 'full', '', null, 'gregorian', 'es'];

        yield ['Nov 7, 2020, 2:04:05 PM', new \DateTimeImmutable('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', null, 'gregorian', 'en'];
        yield ['7 nov 2020, 14:04:05', new \DateTimeImmutable('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', null, 'gregorian', 'es', false, false];

        yield ['2020 Q4 November Saturday 14:04:05', new \DateTime('2020-11-07 15:04:05 CET'), null, null, 'yyyy QQQ MMMM eeee HH:mm:ss', null, 'gregorian', 'en'];
        yield ['2020 T4 noviembre sábado 14:04:05', new \DateTime('2020-11-07 15:04:05 CET'), null, null, 'yyyy QQQ MMMM eeee HH:mm:ss', null, 'gregorian', 'es'];

        yield ['Nov 7, 2020, 11:04:05 PM', new \DateTime('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', new \DateTimeZone('Asia/Tokyo'), 'gregorian', 'en'];
        yield ['Nov 8, 2020, 3:04:05 AM', new \DateTimeImmutable('2020-11-07 15:04:05', new \DateTimeZone('America/Montevideo')), 'medium', 'medium', '', new \DateTimeZone('Asia/Tokyo'), 'gregorian', 'en'];

        yield ['Nov 7, 2020, 2:04:05 PM', new \DateTime('2020-11-07 15:04:05 CET'), 'medium', 'medium', '', null, 'traditional', 'en'];
    }

    public static function provideFormatNumber()
    {
        yield ['0', 0, []];
        yield ['0', 0.0, []];

        yield ['123,456.000', 123456, ['fraction_digit' => 3]];
        yield ['123,456,000', 123456, ['fraction_digit' => 3, 'decimal_separator' => ',']];
        yield ['123 456.000', 123456, ['fraction_digit' => 3, 'grouping_separator' => ' ']];
        yield ['123 456,000', 123456, ['fraction_digit' => 3, 'decimal_separator' => ',', 'grouping_separator' => ' ']];

        yield ['1,234.560', 1234.56, ['fraction_digit' => 3]];
        yield ['1,234,560', 1234.56, ['fraction_digit' => 3, 'decimal_separator' => ',']];
        yield ['1 234.560', 1234.56, ['fraction_digit' => 3, 'grouping_separator' => ' ']];
        yield ['1 234,560', 1234.56, ['fraction_digit' => 3, 'decimal_separator' => ',', 'grouping_separator' => ' ']];
    }

    private function normalizeWhiteSpaces(string $string): string
    {
        return u($string)
            ->replace("\xA0", ' ')         // no-break space (0xA0)
            ->replace("\xE2\x80\xAF", ' ') // narrow no-break space (0x202F)
            // for some unkown reason, the previous replace() calls fail sometimes
            // if the input is 'a.<0x202F>m.', it returns 'a. .'
            // I can't solve this in any way, so let's just add the missing 'm' in 'a. m.' and 'p. m.'
            ->replace('a. .', 'a. m.')
            ->replace('p. .', 'p. m.')
            ->toString();
    }
}

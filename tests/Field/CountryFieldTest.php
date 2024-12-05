<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\CountryConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;

class CountryFieldTest extends AbstractFieldTest
{
    private const NUM_COUNTRIES_AND_REGIONS = 249;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDefaultFieldOptions()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $fieldDto = $this->configure($field);

        self::assertSame('ea-autocomplete', $fieldDto->getFormTypeOption('attr.data-ea-widget'));

        self::assertTrue($fieldDto->getCustomOption(CountryField::OPTION_SHOW_NAME));
        self::assertTrue($fieldDto->getCustomOption(CountryField::OPTION_SHOW_FLAG));
        self::assertSame(CountryField::FORMAT_ISO_3166_ALPHA2, $fieldDto->getCustomOption(CountryField::OPTION_COUNTRY_CODE_FORMAT));
        self::assertNull($fieldDto->getCustomOption(CountryField::OPTION_COUNTRY_CODES_TO_KEEP));
        self::assertNull($fieldDto->getCustomOption(CountryField::OPTION_COUNTRY_CODES_TO_REMOVE));
        self::assertFalse($fieldDto->getCustomOption(CountryField::OPTION_ALLOW_MULTIPLE_CHOICES));
    }

    public function testDefaultOptionsForFormPages()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_NEW);
        $formSelectChoices = $fieldDto->getFormTypeOption(ChoiceField::OPTION_CHOICES);

        $equatorialGuineaChoiceEntryHtml = <<<HTML
            <div class="country-name-flag"><svg xmlns="http://www.w3.org/2000/svg" class="country-flag" height="17" viewBox="0 0 513 342"><path fill="#FFF" d="M0 0h513v342H0z"/><path fill="#6DA544" d="M0 0h513v113.8H0z"/><path fill="#D80027" d="M0 227.6h513V342H0z"/><path fill="#0070C8" d="M126 171 0 342V0z"/><path fill="none" stroke="#000" stroke-miterlimit="10" d="M233.8 139.4v40.4c0 35.6 35.6 35.6 35.6 35.6s35.6 0 35.6-35.6v-40.4h-71.2z"/><path fill="#786145" d="M264.5 179.8h9.8l4 25.8h-17.8z"/><path fill="#6DA544" d="M287.2 162c0-9.8-8-14.8-17.8-14.8s-17.8 5-17.8 14.8c-4.9 0-8.9 4-8.9 8.9s4 8.9 8.9 8.9h35.6c4.9 0 8.9-4 8.9-8.9s-4-8.9-8.9-8.9z"/><g fill="#FFDA00" stroke="#000" stroke-miterlimit="10"><path d="m230.7 120 1.9 3.3h3.8l-1.9 3.3 1.9 3.2h-3.8l-1.9 3.3-1.9-3.3H225l1.9-3.2-1.9-3.3h3.8zM246 120l1.9 3.3h3.7l-1.9 3.3 1.9 3.2h-3.7l-1.9 3.3-1.9-3.3h-3.8l1.9-3.2-1.9-3.3h3.8zM261.3 120l1.9 3.3h3.7l-1.9 3.3 1.9 3.2h-3.7l-1.9 3.3-1.9-3.3h-3.8l1.9-3.2-1.9-3.3h3.8zM277.1 120l1.9 3.3h3.8l-1.9 3.3 1.9 3.2H279l-1.9 3.3-1.9-3.3h-3.7l1.8-3.2-1.8-3.3h3.7zM293.1 120l1.9 3.3h3.8l-1.9 3.3 1.9 3.2H295l-1.9 3.3-1.9-3.3h-3.7l1.8-3.2-1.8-3.3h3.7zM308.1 120l1.9 3.3h3.7l-1.9 3.3 1.9 3.2H310l-1.9 3.3-1.9-3.3h-3.8l1.9-3.2-1.9-3.3h3.8z"/></g><title>Equatorial Guinea</title></svg>\n <span>Equatorial Guinea</span></div>
            HTML;

        self::assertCount(self::NUM_COUNTRIES_AND_REGIONS, $formSelectChoices);
        self::assertSame('GQ', $formSelectChoices[$equatorialGuineaChoiceEntryHtml]);
        self::assertSame('true', $fieldDto->getFormTypeOption('attr.data-ea-autocomplete-render-items-as-html'));
    }

    public function testUnknownCountryCode()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        // the 'es' value is wrong on purpose: country codes must be uppercase
        $field->setValue('es');
        $fieldDto = $this->configure($field);

        self::assertSame('es', $fieldDto->getValue());
        self::assertSame(['UNKNOWN' => 'Unknown "es" country code'], $fieldDto->getFormattedValue());

        // the 'es' value is wrong on purpose: country codes must be uppercase
        $field->setValue(['es', 'KR']);
        $fieldDto = $this->configure($field);

        self::assertSame(['es', 'KR'], $fieldDto->getValue());
        self::assertSame(['UNKNOWN' => 'Unknown "es" country code', 'KR' => 'South Korea'], $fieldDto->getFormattedValue());
    }

    public function testSingleCountryCode()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $field->setValue('ES');
        $fieldDto = $this->configure($field);

        self::assertSame('ES', $fieldDto->getValue());
        self::assertSame(['ES' => 'Spain'], $fieldDto->getFormattedValue());

        $fieldDto = $this->configure($field, requestLocale: 'es');
        self::assertSame('ES', $fieldDto->getValue());
        self::assertSame(['ES' => 'España'], $fieldDto->getFormattedValue());
    }

    public function testMultipleCountryCodes()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $field->setValue(['BD', 'PG', 'SV']);
        $fieldDto = $this->configure($field);

        self::assertSame(['BD', 'PG', 'SV'], $fieldDto->getValue());
        self::assertSame(['BD' => 'Bangladesh', 'PG' => 'Papua New Guinea', 'SV' => 'El Salvador'], $fieldDto->getFormattedValue());

        $fieldDto = $this->configure($field, requestLocale: 'uk');
        self::assertSame(['BD', 'PG', 'SV'], $fieldDto->getValue());
        self::assertSame(['BD' => 'Бангладеш', 'PG' => 'Папуа-Нова Гвінея', 'SV' => 'Сальвадор'], $fieldDto->getFormattedValue());
    }

    public function testRemovingSomeCountries()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $field->remove(['AF', 'KP']);
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_EDIT);
        $formSelectChoices = $fieldDto->getFormTypeOption(ChoiceField::OPTION_CHOICES);
        $formSelectChoicesWithCountryCodesAsKeys = array_flip($formSelectChoices);

        self::assertCount(self::NUM_COUNTRIES_AND_REGIONS - 2, $formSelectChoices);
        self::assertArrayNotHasKey('AF', $formSelectChoicesWithCountryCodesAsKeys);
        self::assertArrayNotHasKey('KP', $formSelectChoicesWithCountryCodesAsKeys);
    }

    public function testShowingOnlySomeCountries()
    {
        $this->initializeConfigurator();

        $menFootballWorldCupWinnerCountries = ['BR', 'DE', 'IT', 'AR', 'FR', 'UY', 'GB', 'ES'];
        $countryCodesSortedAlphabeticallyByCounryEnglishName = ['AR', 'BR', 'FR', 'DE', 'IT', 'ES', 'GB', 'UY'];
        $field = CountryField::new('foo');
        $field->includeOnly($menFootballWorldCupWinnerCountries);
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_EDIT);
        $formSelectChoices = $fieldDto->getFormTypeOption(ChoiceField::OPTION_CHOICES);

        self::assertSame($countryCodesSortedAlphabeticallyByCounryEnglishName, array_values($formSelectChoices));
    }

    public function testShowingWrongCountryCodeInForms()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        // the 'RR' country code does not exist
        $field->includeOnly(['CL', 'RR', 'EG']);
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_EDIT);
        $formSelectChoices = $fieldDto->getFormTypeOption(ChoiceField::OPTION_CHOICES);

        self::assertCount(2, $formSelectChoices);
        self::assertSame(['CL', 'EG'], array_values($formSelectChoices));
    }

    public function testSelectingMultipleChoices()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $field->allowMultipleChoices();
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_EDIT);

        self::assertTrue($fieldDto->getCustomOption(CountryField::OPTION_ALLOW_MULTIPLE_CHOICES));
        self::assertTrue($fieldDto->getFormTypeOption('multiple'));
    }

    public function testUsingAlpha3Format()
    {
        $this->initializeConfigurator();

        $field = CountryField::new('foo');
        $field->useAlpha3Codes();

        // alpha3 shows the right default choices in forms
        $fieldDto = $this->configure($field, pageName: Crud::PAGE_EDIT);
        $formSelectChoices = $fieldDto->getFormTypeOption(ChoiceField::OPTION_CHOICES);
        $formSelectChoicesWithCountryCodesAsKeys = array_flip($formSelectChoices);
        self::assertCount(self::NUM_COUNTRIES_AND_REGIONS, $formSelectChoices);
        self::assertArrayHasKey('MEX', $formSelectChoicesWithCountryCodesAsKeys);
        self::assertArrayNotHasKey('MX', $formSelectChoicesWithCountryCodesAsKeys);

        // valid alpha3 country code in index/detail pages
        $field->setValue('MEX');
        $fieldDto = $this->configure($field);
        self::assertSame('MEX', $fieldDto->getValue());
        self::assertSame(['MX' => 'Mexico'], $fieldDto->getFormattedValue());

        // valid alpha3 country code and localized
        $field->setValue('MEX');
        $fieldDto = $this->configure($field, requestLocale: 'bg');
        self::assertSame('MEX', $fieldDto->getValue());
        self::assertSame(['MX' => 'Мексико'], $fieldDto->getFormattedValue());

        // invalid alpha3 country code
        $field->setValue('MX');
        $fieldDto = $this->configure($field);
        self::assertSame('MX', $fieldDto->getValue());
        self::assertSame(['UNKNOWN' => 'Unknown "MX" country code'], $fieldDto->getFormattedValue());

        // multiple valid alpha3 country codes
        $field->setValue(['MEX', 'VNM']);
        $fieldDto = $this->configure($field);
        self::assertSame(['MEX', 'VNM'], $fieldDto->getValue());
        self::assertSame(['MX' => 'Mexico', 'VN' => 'Vietnam'], $fieldDto->getFormattedValue());
    }

    private function initializeConfigurator(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->configurator = new CountryConfigurator($container->get('twig'));
    }
}

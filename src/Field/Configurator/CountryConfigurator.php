<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Twig\Environment;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CountryConfigurator implements FieldConfiguratorInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return CountryField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $field->setFormTypeOption('attr.data-ea-widget', 'ea-autocomplete');
        $countryCodeFormat = $field->getCustomOption(CountryField::OPTION_COUNTRY_CODE_FORMAT);
        $field->setFormattedValue($this->getCountryNames((array) $field->getValue(), $countryCodeFormat, $context->getRequest()->getLocale()));

        if (null === $field->getTextAlign() && false === $field->getCustomOption(CountryField::OPTION_SHOW_NAME)) {
            $field->setTextAlign(TextAlign::CENTER);
        }

        if (\in_array($context->getCrud()->getCurrentPage(), [Crud::PAGE_EDIT, Crud::PAGE_NEW], true)) {
            // country names passed to the form are already translated, so don't translate them again in the template
            $field->setFormTypeOptionIfNotSet('choice_translation_domain', false);
            $field->setFormTypeOption('choices', $this->generateFormTypeChoices($countryCodeFormat, $field->getCustomOption(CountryField::OPTION_COUNTRY_CODES_TO_KEEP), $field->getCustomOption(CountryField::OPTION_COUNTRY_CODES_TO_REMOVE)));

            // the value of this form option must be a string to properly propagate it as an HTML attribute value
            $field->setFormTypeOption('attr.data-ea-autocomplete-render-items-as-html', 'true');

            if (true === $field->getCustomOption(CountryField::OPTION_ALLOW_MULTIPLE_CHOICES)) {
                $field->setFormTypeOption('multiple', true);
            }
        }
    }

    private function getCountryNames(?array $countryCodes, string $countryCodeFormat, string $displayLocale): ?array
    {
        if (null === $countryCodes) {
            return null;
        }

        $countryNames = [];
        $usesAlpha3Codes = CountryField::FORMAT_ISO_3166_ALPHA3 === $countryCodeFormat;
        foreach ($countryCodes as $countryCode) {
            if (null === $countryCode) {
                continue;
            }

            try {
                $alpha2CountryCode = $usesAlpha3Codes ? Countries::getAlpha2Code($countryCode) : $countryCode;
                $countryNames[$alpha2CountryCode] = $usesAlpha3Codes ? Countries::getAlpha3Name($countryCode, $displayLocale) : Countries::getName($countryCode, $displayLocale);
            } catch (MissingResourceException) {
                $countryNames['UNKNOWN'] = sprintf('Unknown "%s" country code', $countryCode);
            }
        }

        return $countryNames;
    }

    private function generateFormTypeChoices(string $countryCodeFormat, ?array $countryCodesToKeep, ?array $countryCodesToRemove): array
    {
        $usesAlpha3Codes = CountryField::FORMAT_ISO_3166_ALPHA3 === $countryCodeFormat;
        $choices = [];

        $countries = $usesAlpha3Codes ? Countries::getAlpha3Names() : Countries::getNames();
        foreach ($countries as $countryCode => $countryName) {
            if (null !== $countryCodesToKeep && !\in_array($countryCode, $countryCodesToKeep, true)) {
                continue;
            }

            if (null !== $countryCodesToRemove && \in_array($countryCode, $countryCodesToRemove, true)) {
                continue;
            }

            $countryCodeAlpha2 = $usesAlpha3Codes ? Countries::getAlpha2Code($countryCode) : $countryCode;
            $choiceKey = $this->twig->createTemplate(sprintf('<div class="country-name-flag"><twig:ea:Flag countryCode="%s" /> <span>%s</span></div>', $countryCodeAlpha2, $countryName))->render();

            $choices[$choiceKey] = $countryCode;
        }

        return $choices;
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CountryField implements FieldInterface
{
    use FieldTrait;

    public const FORMAT_ISO_3166_ALPHA2 = 'iso3166Alpha2';
    public const FORMAT_ISO_3166_ALPHA3 = 'iso3166Alpha3';

    public const OPTION_SHOW_FLAG = 'showFlag';
    public const OPTION_SHOW_NAME = 'showName';
    public const OPTION_COUNTRY_CODE_FORMAT = 'countryCodeFormat';
    public const OPTION_COUNTRY_CODES_TO_KEEP = 'countryCodesToKeep';
    public const OPTION_COUNTRY_CODES_TO_REMOVE = 'countryCodesToRemove';
    public const OPTION_ALLOW_MULTIPLE_CHOICES = 'allowMultipleChoices';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/country')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-country')
            ->setDefaultColumns('col-md-4 col-xxl-3')
            ->setCustomOption(self::OPTION_SHOW_FLAG, true)
            ->setCustomOption(self::OPTION_SHOW_NAME, true)
            ->setCustomOption(self::OPTION_COUNTRY_CODE_FORMAT, self::FORMAT_ISO_3166_ALPHA2)
            ->setCustomOption(self::OPTION_COUNTRY_CODES_TO_KEEP, null)
            ->setCustomOption(self::OPTION_COUNTRY_CODES_TO_REMOVE, null)
            ->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, false);
    }

    public function showFlag(bool $isShown = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_FLAG, $isShown);

        return $this;
    }

    public function showName(bool $isShown = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_NAME, $isShown);

        return $this;
    }

    public function useAlpha3Codes(bool $useAlpha3 = true): self
    {
        $this->setCustomOption(self::OPTION_COUNTRY_CODE_FORMAT, $useAlpha3 ? self::FORMAT_ISO_3166_ALPHA3 : self::FORMAT_ISO_3166_ALPHA2);

        return $this;
    }

    /**
     * Restricts the list of countries shown by the field to the given list of country codes.
     * e.g. ->includeOnly(['AR', 'BR', 'ES', 'PT']).
     */
    public function includeOnly(array $countryCodesToKeep): self
    {
        $this->setCustomOption(self::OPTION_COUNTRY_CODES_TO_KEEP, $countryCodesToKeep);

        return $this;
    }

    /**
     * Removes the given list of country codes from the countries displayed by the field.
     * e.g. ->remove(['AF', 'KP']).
     */
    public function remove(array $countryCodesToRemove): self
    {
        $this->setCustomOption(self::OPTION_COUNTRY_CODES_TO_REMOVE, $countryCodesToRemove);

        return $this;
    }

    public function allowMultipleChoices(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, $allow);

        return $this;
    }
}

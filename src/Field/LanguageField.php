<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class LanguageField implements FieldInterface
{
    use FieldTrait;

    public const FORMAT_ISO_639_ALPHA2 = 'iso639Alpha2';
    public const FORMAT_ISO_639_ALPHA3 = 'iso639Alpha3';

    public const OPTION_SHOW_CODE = 'showCode';
    public const OPTION_SHOW_NAME = 'showName';
    public const OPTION_LANGUAGE_CODE_FORMAT = 'languageCodeFormat';
    public const OPTION_LANGUAGE_CODES_TO_KEEP = 'languageCodesToKeep';
    public const OPTION_LANGUAGE_CODES_TO_REMOVE = 'languageCodesToRemove';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/language')
            ->setFormType(LanguageType::class)
            ->addCssClass('field-language')
            ->setDefaultColumns('col-md-4 col-xxl-3')
            ->setCustomOption(self::OPTION_SHOW_CODE, false)
            ->setCustomOption(self::OPTION_SHOW_NAME, true)
            ->setCustomOption(self::OPTION_LANGUAGE_CODE_FORMAT, self::FORMAT_ISO_639_ALPHA2)
            ->setCustomOption(self::OPTION_LANGUAGE_CODES_TO_KEEP, null)
            ->setCustomOption(self::OPTION_LANGUAGE_CODES_TO_REMOVE, null);
    }

    public function showCode(bool $isShown = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_CODE, $isShown);

        return $this;
    }

    public function showName(bool $isShown = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_NAME, $isShown);

        return $this;
    }

    public function useAlpha3Codes(bool $useAlpha3 = true): self
    {
        $this->setCustomOption(self::OPTION_LANGUAGE_CODE_FORMAT, $useAlpha3 ? self::FORMAT_ISO_639_ALPHA3 : self::FORMAT_ISO_639_ALPHA2);

        return $this;
    }

    /**
     * Restricts the list of languages shown by the field to the given list of languages codes.
     * e.g. ->includeOnly(['de', 'en', 'fr']).
     */
    public function includeOnly(array $countryCodesToKeep): self
    {
        $this->setCustomOption(self::OPTION_LANGUAGE_CODES_TO_KEEP, $countryCodesToKeep);

        return $this;
    }

    /**
     * Removes the given list of languages codes from the languages displayed by the field.
     * e.g. ->remove(['de', 'fr']).
     */
    public function remove(array $countryCodesToRemove): self
    {
        $this->setCustomOption(self::OPTION_LANGUAGE_CODES_TO_REMOVE, $countryCodesToRemove);

        return $this;
    }
}

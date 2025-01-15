<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class LocaleField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_SHOW_CODE = 'showCode';
    public const OPTION_SHOW_NAME = 'showName';
    public const OPTION_LOCALE_CODES_TO_KEEP = 'localeCodesToKeep';
    public const OPTION_LOCALE_CODES_TO_REMOVE = 'localeCodesToRemove';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/locale')
            ->setFormType(LocaleType::class)
            ->addCssClass('field-locale')
            ->setDefaultColumns('col-md-5 col-xxl-4')
            ->setCustomOption(self::OPTION_SHOW_CODE, false)
            ->setCustomOption(self::OPTION_SHOW_NAME, true)
            ->setCustomOption(self::OPTION_LOCALE_CODES_TO_KEEP, null)
            ->setCustomOption(self::OPTION_LOCALE_CODES_TO_REMOVE, null);
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

    /**
     * Restricts the list of locales shown by the field to the given list of locale codes.
     * e.g. ->includeOnly(['de', 'en', 'fr']).
     */
    public function includeOnly(array $countryCodesToKeep): self
    {
        $this->setCustomOption(self::OPTION_LOCALE_CODES_TO_KEEP, $countryCodesToKeep);

        return $this;
    }

    /**
     * Removes the given list of locale codes from the locales displayed by the field.
     * e.g. ->remove(['de', 'fr']).
     */
    public function remove(array $countryCodesToRemove): self
    {
        $this->setCustomOption(self::OPTION_LOCALE_CODES_TO_REMOVE, $countryCodesToRemove);

        return $this;
    }
}

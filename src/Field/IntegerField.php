<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class IntegerField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUMBER_FORMAT = 'numberFormat';
    public const OPTION_THOUSANDS_SEPARATOR = 'thousandsSeparator';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/integer')
            ->setFormType(IntegerType::class)
            ->addCssClass('field-integer')
            ->setDefaultColumns('col-md-4 col-xxl-3')
            ->setCustomOption(self::OPTION_NUMBER_FORMAT, null)
            ->setCustomOption(self::OPTION_THOUSANDS_SEPARATOR, null);
    }

    // this format is passed directly to the first argument of `sprintf()` to format the integer before displaying it
    public function setNumberFormat(string $sprintfFormat): self
    {
        $this->setCustomOption(self::OPTION_NUMBER_FORMAT, $sprintfFormat);

        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->setCustomOption(self::OPTION_THOUSANDS_SEPARATOR, $separator);

        return $this;
    }
}

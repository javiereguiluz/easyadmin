<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class Field implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label);
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
interface FieldInterface
{
    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null);

    public function getAsDto(): FieldDto;

    public function __clone(): void;
}

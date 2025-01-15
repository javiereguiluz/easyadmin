<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class UrlField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/url')
            ->setFormType(UrlType::class)
            ->addCssClass('field-url')
            ->setDefaultColumns('col-md-10 col-xxl-8');
    }
}

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

    public const OPTION_DEFAULT_PROTOCOL = 'defaultProtocol';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/url')
            ->setFormType(UrlType::class)
            ->addCssClass('field-url')
            ->setDefaultColumns('col-md-10 col-xxl-8')
            ->setCustomOption(self::OPTION_DEFAULT_PROTOCOL, null);
    }

    /**
     * Defines the protocol prepended to the URL if it doesn't include it.
     * If not set, no protocol is prepended to the URL and the field is rendered
     * using an <input type="url"> HTML element to enable local browser validation.
     * See https://symfony.com/doc/current/reference/forms/types/url.html#default-protocol.
     */
    public function setDefaultProtocol(string $protocol): self
    {
        $this->setCustomOption(self::OPTION_DEFAULT_PROTOCOL, $protocol);

        return $this;
    }
}

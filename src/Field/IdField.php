<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class IdField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_MAX_LENGTH = 'maxLength';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/id')
            ->setFormType(TextType::class)
            ->addCssClass('field-id')
            ->setDefaultColumns('col-md-6 col-xxl-5')
            ->setCustomOption(self::OPTION_MAX_LENGTH, null);
    }

    /**
     * Set maxLength to -1 to define an unlimited max length.
     */
    public function setMaxLength(int $length): self
    {
        if (0 === $length) {
            throw new \InvalidArgumentException(sprintf('The argument of the "%s()" method must be a positive integer or -1 (for unlimited length) (%d given).', __METHOD__, $length));
        }

        $this->setCustomOption(self::OPTION_MAX_LENGTH, $length);

        return $this;
    }
}

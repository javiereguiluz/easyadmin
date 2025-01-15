<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Currencies;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MoneyField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_CURRENCY = 'currency';
    public const OPTION_CURRENCY_PROPERTY_PATH = 'currencyPropertyPath';
    public const OPTION_NUM_DECIMALS = 'numDecimals';
    public const OPTION_STORED_AS_CENTS = 'storedAsCents';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/money')
            ->setFormType(MoneyType::class)
            ->addCssClass('field-money')
            ->setTextAlign(TextAlign::RIGHT)
            ->setDefaultColumns('col-md-6 col-xxl-5')
            ->setCustomOption(self::OPTION_CURRENCY, null)
            ->setCustomOption(self::OPTION_CURRENCY_PROPERTY_PATH, null)
            ->setCustomOption(self::OPTION_NUM_DECIMALS, 2)
            ->setCustomOption(self::OPTION_STORED_AS_CENTS, true);
    }

    public function setCurrency(string $currencyCode): self
    {
        if (!Currencies::exists($currencyCode)) {
            throw new \InvalidArgumentException(sprintf('The argument of the "%s()" method must be a valid currency code according to ICU data ("%s" given).', __METHOD__, $currencyCode));
        }

        $this->setCustomOption(self::OPTION_CURRENCY, $currencyCode);

        return $this;
    }

    public function setCurrencyPropertyPath(string $propertyPath): self
    {
        $this->setCustomOption(self::OPTION_CURRENCY_PROPERTY_PATH, $propertyPath);

        return $this;
    }

    public function setNumDecimals(int $num): self
    {
        if ($num < 0) {
            throw new \InvalidArgumentException(sprintf('The argument of the "%s()" method must be 0 or higher (%d given).', __METHOD__, $num));
        }

        $this->setCustomOption(self::OPTION_NUM_DECIMALS, $num);

        return $this;
    }

    public function setStoredAsCents(bool $asCents = true): self
    {
        $this->setCustomOption(self::OPTION_STORED_AS_CENTS, $asCents);

        return $this;
    }
}

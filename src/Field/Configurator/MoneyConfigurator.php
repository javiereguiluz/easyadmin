<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Intl\IntlFormatterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MoneyConfigurator implements FieldConfiguratorInterface
{
    private IntlFormatterInterface $intlFormatter;
    private PropertyAccessorInterface $propertyAccessor;

    public const DEFAULT_DIVISOR = 100;

    public function __construct(IntlFormatterInterface $intlFormatter, PropertyAccessorInterface $propertyAccessor)
    {
        $this->intlFormatter = $intlFormatter;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return MoneyField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $currencyCode = $this->getCurrency($field, $entityDto);
        if (null !== $currencyCode && !Currencies::exists($currencyCode)) {
            throw new \InvalidArgumentException(sprintf('The "%s" value used as the currency of the "%s" money field is not a valid ICU currency code.', $currencyCode, $field->getProperty()));
        }
        $field->setFormTypeOption('currency', $currencyCode);

        $numDecimals = $field->getCustomOption(MoneyField::OPTION_NUM_DECIMALS);
        $field->setFormTypeOption('scale', $numDecimals);

        $isStoredAsCents = true === $field->getCustomOption(MoneyField::OPTION_STORED_AS_CENTS);
        $field->setFormTypeOptionIfNotSet('divisor', $isStoredAsCents ? self::DEFAULT_DIVISOR : 1);

        if (null === $field->getValue()) {
            return;
        }

        $divisor = $field->getFormTypeOption('divisor');
        $amount = $field->getValue() / $divisor;
        $formattedValue = $this->intlFormatter->formatCurrency($amount, $currencyCode, ['fraction_digit' => $numDecimals]);
        $field->setFormattedValue($formattedValue);
    }

    private function getCurrency(FieldDto $field, EntityDto $entityDto): ?string
    {
        if (null !== $currencyCode = $field->getCustomOption(MoneyField::OPTION_CURRENCY)) {
            return $currencyCode;
        }

        if (null === $currencyPropertyPath = $field->getCustomOption(MoneyField::OPTION_CURRENCY_PROPERTY_PATH)) {
            throw new \InvalidArgumentException(sprintf('You must define the currency for the "%s" money field.', $field->getProperty()));
        }

        if (null === $field->getValue()) {
            return null;
        }

        $entityInstance = $entityDto->getInstance();
        $isPropertyReadable = (null !== $entityInstance) && $this->propertyAccessor->isReadable($entityInstance, $currencyPropertyPath);
        if (!$isPropertyReadable) {
            throw new \InvalidArgumentException(sprintf('The "%s" field path used by the "%s" field to get the currency value from the "%s" entity is not readable.', $currencyPropertyPath, $field->getProperty(), $entityDto->getName()));
        }

        if (null === $currencyCode = $this->propertyAccessor->getValue($entityInstance, $currencyPropertyPath)) {
            throw new \InvalidArgumentException(sprintf('The currency value for the "%s" field cannot be null, but that\'s the value returned by the "%s" field path applied on the "%s" entity.', $field->getProperty(), $currencyPropertyPath, $entityDto->getName()));
        }

        return $currencyCode;
    }
}

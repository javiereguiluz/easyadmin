<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CurrencyField;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CurrencyConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        return CurrencyField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDtoInterface $field, EntityDtoInterface $entityDto, AdminContext $context): void
    {
        $field->setFormTypeOptionIfNotSet('attr.data-ea-widget', 'ea-autocomplete');

        if (null === $currencyCode = $field->getValue()) {
            return;
        }

        $currencyName = $this->getCurrencyName($currencyCode);
        if (null === $currencyName) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" value used as the currency code of the "%s" field is not a valid ICU currency code.',
                    $currencyCode,
                    $field->getProperty()
                )
            );
        }

        $currencySymbol = $this->getCurrencySymbol($currencyCode);
        if (null === $currencySymbol) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The "%s" value used as the currency code of the "%s" field has no valid ICU currency symbol associated to it.',
                    $currencyCode,
                    $field->getProperty()
                )
            );
        }

        $field->setFormattedValue([
            'name' => $currencyName,
            'symbol' => $currencySymbol,
        ]);
    }

    private function getCurrencyName(string $currencyCode): ?string
    {
        try {
            return Currencies::getName($currencyCode);
        } catch (MissingResourceException) {
            return null;
        }
    }

    private function getCurrencySymbol(string $currencyCode): ?string
    {
        try {
            return Currencies::getSymbol($currencyCode);
        } catch (MissingResourceException) {
            return null;
        }
    }
}

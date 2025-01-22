<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Intl\IntlFormatterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class PercentConfigurator implements FieldConfiguratorInterface
{
    private IntlFormatterInterface $intlFormatter;

    public function __construct(IntlFormatterInterface $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return PercentField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $scale = $field->getCustomOption(PercentField::OPTION_NUM_DECIMALS);
        $roundingMode = $field->getCustomOption(PercentField::OPTION_ROUNDING_MODE);
        $symbol = $field->getCustomOption(PercentField::OPTION_SYMBOL);
        $isStoredAsFractional = true === $field->getCustomOption(PercentField::OPTION_STORED_AS_FRACTIONAL);

        $formatterAttributes = ['rounding_mode' => $this->getRoundingModeAsString($roundingMode)];
        if (null !== $scale) {
            $formatterAttributes['fraction_digit'] = $scale;
        }

        $field->setFormTypeOptionIfNotSet('scale', $scale);
        $field->setFormTypeOptionIfNotSet('symbol', $symbol);
        $field->setFormTypeOptionIfNotSet('type', $isStoredAsFractional ? 'fractional' : 'integer');
        $field->setFormTypeOptionIfNotSet('rounding_mode', $roundingMode);

        if (null === $value = $field->getValue()) {
            return;
        }

        $value = $isStoredAsFractional ? 100 * $value : $value;

        $field->setFormattedValue(sprintf('%s%s', $this->intlFormatter->formatNumber($value, $formatterAttributes), $symbol));
    }

    private function getRoundingModeAsString(int $mode): string
    {
        return [
            \NumberFormatter::ROUND_DOWN => 'down',
            \NumberFormatter::ROUND_FLOOR => 'floor',
            \NumberFormatter::ROUND_UP => 'up',
            \NumberFormatter::ROUND_CEILING => 'ceiling',
            \NumberFormatter::ROUND_HALFDOWN => 'halfdown',
            \NumberFormatter::ROUND_HALFEVEN => 'halfeven',
            \NumberFormatter::ROUND_HALFUP => 'halfup',
        ][$mode];
    }
}

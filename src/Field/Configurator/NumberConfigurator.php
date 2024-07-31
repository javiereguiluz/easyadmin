<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class NumberConfigurator implements FieldConfiguratorInterface
{
    private IntlFormatter $intlFormatter;
    private AdminUrlGeneratorInterface $adminUrlGenerator;
    private ?CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        IntlFormatter $intlFormatter,
        AdminUrlGeneratorInterface $adminUrlGenerator,
        ?CsrfTokenManagerInterface $csrfTokenManager = null
    ) {
        $this->intlFormatter = $intlFormatter;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return NumberField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $scale = $field->getCustomOption(NumberField::OPTION_NUM_DECIMALS);
        $roundingMode = $field->getCustomOption(NumberField::OPTION_ROUNDING_MODE);
        $isStoredAsString = true === $field->getCustomOption(NumberField::OPTION_STORED_AS_STRING);

        $field->setFormTypeOptionIfNotSet('input', $isStoredAsString ? 'string' : 'number');
        $field->setFormTypeOptionIfNotSet('rounding_mode', $roundingMode);
        $field->setFormTypeOptionIfNotSet('scale', $scale);

        if (null === $value = $field->getValue()) {
            return;
        }

        $formatterAttributes = ['rounding_mode' => $this->getRoundingModeAsString($roundingMode)];
        if (null !== $scale) {
            $formatterAttributes['fraction_digit'] = $scale;
        }

        $numberFormat = $field->getCustomOption(NumberField::OPTION_NUMBER_FORMAT)
            ?? $context->getCrud()->getNumberFormat()
            ?? null;

        if (null !== $numberFormat) {
            $field->setFormattedValue(sprintf($numberFormat, $value));

            return;
        }

        $thousandsSeparator = $field->getCustomOption(NumberField::OPTION_THOUSANDS_SEPARATOR)
            ?? $context->getCrud()->getThousandsSeparator()
            ?? null;
        if (null !== $thousandsSeparator) {
            $formatterAttributes['grouping_separator'] = $thousandsSeparator;
        }

        $decimalSeparator = $field->getCustomOption(NumberField::OPTION_DECIMAL_SEPARATOR)
            ?? $context->getCrud()->getDecimalSeparator()
            ?? null;
        if (null !== $decimalSeparator) {
            $formatterAttributes['decimal_separator'] = $decimalSeparator;
        }

        $field->setFormattedValue($this->intlFormatter->formatNumber($value, $formatterAttributes));

        $crudDto = $context->getCrud();

        if (null !== $crudDto && Action::NEW !== $crudDto->getCurrentAction()) {
            $toggleUrl = $this->adminUrlGenerator
                ->setAction(Action::EDIT)
                ->setEntityId($entityDto->getPrimaryKeyValue())
                ->set('fieldName', $field->getProperty())
                ->set('csrfToken', $this->csrfTokenManager?->getToken(BooleanField::CSRF_TOKEN_NAME))
                ->generateUrl();
            $field->setCustomOption(NumberField::OPTION_TOGGLE_URL, $toggleUrl);
        }
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

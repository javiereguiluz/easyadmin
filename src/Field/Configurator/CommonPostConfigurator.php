<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\CrudInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProviderInterface;
use Twig\Markup;

use function Symfony\Component\String\u;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CommonPostConfigurator implements FieldConfiguratorInterface
{
    private AdminContextProviderInterface $adminContextProvider;

    private string $charset;

    public function __construct(AdminContextProviderInterface $adminContextProvider, string $charset)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->charset = $charset;
    }

    public function supports(FieldDtoInterface $field, EntityDtoInterface $entityDto): bool
    {
        // this configurator applies to all kinds of properties
        return true;
    }

    public function configure(FieldDtoInterface $field, EntityDtoInterface $entityDto, AdminContext $context): void
    {
        if (\in_array(
            $context->getCrud()->getCurrentPage(),
            [CrudInterface::PAGE_INDEX, CrudInterface::PAGE_DETAIL],
            true
        )) {
            $formattedValue = $this->buildFormattedValueOption($field->getFormattedValue(), $field, $entityDto);
            $field->setFormattedValue($formattedValue);
        }

        $this->updateFieldTemplate($field);
    }

    private function buildFormattedValueOption($value, FieldDtoInterface $field, EntityDtoInterface $entityDto)
    {
        if (null === $callable = $field->getFormatValueCallable()) {
            return $value;
        }

        $formatted = $callable($value, $entityDto->getInstance());

        // if the callable returns a string, wrap it in a Twig Markup to render the
        // HTML and CSS/JS elements that it might contain
        return \is_string($formatted) ? new Markup($formatted, $this->charset) : $formatted;
    }

    private function updateFieldTemplate(FieldDtoInterface $field): void
    {
        $usesEasyAdminTemplate = u($field->getTemplatePath())->startsWith('@EasyAdmin/');
        $isBooleanField = BooleanField::class === $field->getFieldFqcn();
        $isNullValue = null === $field->getFormattedValue();
        $isEmpty = is_countable($field->getFormattedValue()) && 0 === \count($field->getFormattedValue());

        $adminContext = $this->adminContextProvider->getContext();
        if ($usesEasyAdminTemplate && $isNullValue && !$isBooleanField) {
            $field->setTemplatePath($adminContext->getTemplatePath('label/null'));
        }

        if ($usesEasyAdminTemplate && $isEmpty) {
            $field->setTemplatePath($adminContext->getTemplatePath('label/empty'));
        }
    }
}

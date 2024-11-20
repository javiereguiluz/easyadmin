<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use function Symfony\Component\String\u;
use Twig\Markup;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CommonPostConfigurator implements FieldConfiguratorInterface
{
    private AdminContextProvider $adminContextProvider;
    private string $charset;

    public function __construct(AdminContextProvider $adminContextProvider, string $charset)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->charset = $charset;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        // this configurator applies to all kinds of properties
        return true;
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        // EasyAdmin by default allows using HTML contents in labels, help messages, etc.
        // so we must enable the 'label_html' form option unless a field has configured it explicitly
        $field->setFormTypeOptionIfNotSet('label_html', true);

        if (\in_array($context->getCrud()->getCurrentPage(), [Crud::PAGE_INDEX, Crud::PAGE_DETAIL], true)) {
            $formattedValue = $this->buildFormattedValueOption($field->getFormattedValue(), $field, $entityDto);
            $field->setFormattedValue($formattedValue);
        }

        $this->updateFieldTemplate($field);
    }

    private function buildFormattedValueOption($value, FieldDto $field, EntityDto $entityDto)
    {
        if (null === $callable = $field->getFormatValueCallable()) {
            return $value;
        }

        $formatted = $callable($field->getValue(), $entityDto->getInstance());

        // we don't want to unintentionally allow people to add XSS vulnerabilities
        // in the code just because some people need to have HTML/JS
        // so that if you want know what you're doing you have to explicitly
        // disable this.
        if ($field->getCustomOption(FieldTrait::OPTION_STRIP_TAGS) ?? true) {
            return $formatted;
        }

        // if the callable returns a string, wrap it in a Twig Markup to render the
        // HTML and CSS/JS elements that it might contain
        return \is_string($formatted) ? new Markup($formatted, $this->charset) : $formatted;
    }

    private function updateFieldTemplate(FieldDto $field): void
    {
        $usesEasyAdminTemplate = u($field->getTemplatePath())->startsWith('@EasyAdmin/');
        $isNullValue = null === $field->getFormattedValue();
        $isEmpty = is_countable($field->getFormattedValue()) && 0 === \count($field->getFormattedValue());

        $adminContext = $this->adminContextProvider->getContext();
        if ($usesEasyAdminTemplate && $isNullValue) {
            $field->setTemplatePath($adminContext->getTemplatePath('label/null'));
        }

        if ($usesEasyAdminTemplate && $isEmpty) {
            $field->setTemplatePath($adminContext->getTemplatePath('label/empty'));
        }
    }
}

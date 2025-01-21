<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\UrlConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class UrlFieldTest extends AbstractFieldTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDefaultFieldOptions()
    {
        $this->initializeConfigurator();

        $field = UrlField::new('foo');
        $fieldDto = $this->configure($field, actionName: Action::EDIT);

        self::assertSame('url', $fieldDto->getFormTypeOption('attr.inputmode'));
        self::assertNull($fieldDto->getCustomOption(UrlField::OPTION_DEFAULT_PROTOCOL));
    }

    /**
     * @testWith [""]
     *           ["http"]
     *           ["https"]
     *           ["ftp"]
     */
    public function testDefaultProtocolOption(string $defaultProtocol)
    {
        $this->initializeConfigurator();

        $field = UrlField::new('foo');
        $field->setDefaultProtocol($defaultProtocol);
        $fieldDto = $this->configure($field, actionName: Action::EDIT);

        self::assertSame($defaultProtocol, $fieldDto->getCustomOption(UrlField::OPTION_DEFAULT_PROTOCOL));
        self::assertSame($defaultProtocol, $fieldDto->getFormTypeOption('default_protocol'));
    }

    /**
     * @testWith ["http://example.com", "example.com"]
     *           ["https://example.com", "example.com"]
     *           ["http://www.example.com", "example.com"]
     *           ["https://www.example.com", "example.com"]
     *           ["https://01234567890123456789012345678901234567890123456789.com", "0123456789012345678901234567890…"]
     */
    public function testFormattedValuesOnIndexAction(string $url, string $expectedRenderedUrl)
    {
        $this->initializeConfigurator();

        $field = UrlField::new('foo')->setValue($url);
        $fieldDto = $this->configure($field);

        self::assertSame($expectedRenderedUrl, $fieldDto->getFormattedValue());
    }

    private function initializeConfigurator(): void
    {
        self::bootKernel();
        $this->configurator = new UrlConfigurator();
    }
}

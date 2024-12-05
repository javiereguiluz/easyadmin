<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class FormFieldTest extends TestCase
{
    /** @dataProvider defaultPropertySuffixProvider */
    public function testDefaultSetPropertySuffix(FormField $formField)
    {
        $this->assertTrue(Ulid::isValid($formField->getAsDto()->getPropertyNameSuffix()));
    }

    public function defaultPropertySuffixProvider(): \Generator
    {
        yield [FormField::addFieldset()];
        yield [FormField::addColumn()];
        yield [FormField::addRow()];
        yield [FormField::addTab()];
    }

    /** @dataProvider propertySuffixProvider */
    public function testSetPropertySuffix(FormField $formField, string $expectedPropertyName, string $expectedPropertyNameSuffix)
    {
        $dto = $formField->getAsDto();
        $this->assertSame($expectedPropertyName, $dto->getPropertyNameWithSuffix());
        $this->assertSame($expectedPropertyNameSuffix, $dto->getPropertyNameSuffix());
    }

    public function propertySuffixProvider(): \Generator
    {
        yield [FormField::addFieldset()->setPropertySuffix('foo'), 'ea_form_fieldset_foo', 'foo'];
        yield [FormField::addColumn()->setPropertySuffix('foo'), 'ea_form_column_foo', 'foo'];
        yield [FormField::addRow()->setPropertySuffix('foo'), 'ea_form_row_foo', 'foo'];
        yield [FormField::addTab()->setPropertySuffix('foo'), 'ea_form_tab_foo', 'foo'];
    }
}

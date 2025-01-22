<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\DateTimeConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;

class DateTimeFieldTest extends AbstractFieldTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->configurator = new DateTimeConfigurator(new IntlFormatter());
    }

    public function testFieldWithWrongTimezone()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setTimezone('this-timezone-does-not-exist');
    }

    public function testFieldWithoutTimezone()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $fieldDto = $this->configure($field);

        $this->assertNull($fieldDto->getCustomOption(DateTimeField::OPTION_TIMEZONE));
    }

    public function testFieldWithTimezone()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setTimezone('Europe/Madrid');
        $fieldDto = $this->configure($field);

        $this->assertSame('Europe/Madrid', $fieldDto->getCustomOption(DateTimeField::OPTION_TIMEZONE));
    }

    public function testFieldWithWrongFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat(DateTimeField::FORMAT_NONE);
    }

    public function testFieldWithEmptyDateFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat('');
    }

    public function testFieldWithEmptyDateAndTimeFormats()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat('', '');
    }

    public function testFieldWithNoneDateAndTimeFormats()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat(DateTimeField::FORMAT_NONE, DateTimeField::FORMAT_NONE);
    }

    public function testFieldWithPredefinedFormat()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat(DateTimeField::FORMAT_LONG);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::FORMAT_LONG, $fieldDto->getCustomOption(DateTimeField::OPTION_DATE_PATTERN));
        $this->assertSame('January 16, 2015', $fieldDto->getFormattedValue());
    }

    public function testFieldWithCustomPattern()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->setFormat('HH:mm:ss ZZZZ a');
        $fieldDto = $this->configure($field);

        $this->assertSame('HH:mm:ss ZZZZ a', $fieldDto->getCustomOption(DateTimeField::OPTION_DATE_PATTERN));
        $this->assertSame('00:00:00 GMT AM', $fieldDto->getFormattedValue());
    }

    public function testFieldDefaultWidget()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsNativeWidget()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsNativeWidget();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
        $this->assertSame('single_text', $fieldDto->getFormTypeOption('widget'));
        $this->assertTrue($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotNativeWidget()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsNativeWidget(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_CHOICE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsChoice()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsChoice();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_CHOICE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
        $this->assertSame('choice', $fieldDto->getFormTypeOption('widget'));
        $this->assertTrue($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotChoice()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsChoice(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsText()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsText();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_TEXT, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
        $this->assertSame('single_text', $fieldDto->getFormTypeOption('widget'));
        $this->assertFalse($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotText()
    {
        $field = DateTimeField::new('foo')->setValue(new \DateTime('2015-01-16'));
        $field->setFieldFqcn(DateTimeField::class);
        $field->renderAsText(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(DateTimeField::OPTION_WIDGET));
    }
}

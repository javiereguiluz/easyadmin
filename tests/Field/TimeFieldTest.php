<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\DateTimeConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;

class TimeFieldTest extends AbstractFieldTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->configurator = new DateTimeConfigurator(new IntlFormatter());
    }

    public function testFieldWithWrongTimezone()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = TimeField::new('foo');
        $field->setTimezone('this-timezone-does-not-exist');
    }

    public function testFieldWithoutTimezone()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $fieldDto = $this->configure($field);

        $this->assertNull($fieldDto->getCustomOption(DateTimeField::OPTION_TIMEZONE));
    }

    public function testFieldWithTimezone()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->setTimezone('Europe/Madrid');
        $fieldDto = $this->configure($field);

        $this->assertSame('Europe/Madrid', $fieldDto->getCustomOption(DateTimeField::OPTION_TIMEZONE));
    }

    public function testFieldWithWrongFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->setFormat(DateTimeField::FORMAT_NONE);
    }

    public function testFieldWithEmptyFormat()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->setFormat('');
    }

    public function testFieldWithPredefinedFormat()
    {
        $field = TimeField::new('foo')->setValue(new \DateTime('2006-01-02 15:04:05'));
        $field->setFieldFqcn(TimeField::class);
        $field->setFormat(DateTimeField::FORMAT_LONG);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::FORMAT_LONG, $fieldDto->getCustomOption(TimeField::OPTION_TIME_PATTERN));
        $this->assertSame('3:04:05 PM UTC', $fieldDto->getFormattedValue());
    }

    public function testFieldWithCustomPattern()
    {
        $field = TimeField::new('foo')->setValue(new \DateTime('2006-01-02 15:04:05'));
        $field->setFieldFqcn(TimeField::class);
        $field->setFormat('HH:mm:ss ZZZZ a');
        $fieldDto = $this->configure($field);

        $this->assertSame('HH:mm:ss ZZZZ a', $fieldDto->getCustomOption(TimeField::OPTION_TIME_PATTERN));
        $this->assertSame('15:04:05 GMT PM', $fieldDto->getFormattedValue());
    }

    public function testFieldDefaultWidget()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsNativeWidget()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsNativeWidget();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
        $this->assertSame('single_text', $fieldDto->getFormTypeOption('widget'));
        $this->assertTrue($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotNativeWidget()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsNativeWidget(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_CHOICE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsChoice()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsChoice();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_CHOICE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
        $this->assertSame('choice', $fieldDto->getFormTypeOption('widget'));
        $this->assertTrue($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotChoice()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsChoice(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
    }

    public function testFieldRenderAsText()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsText();
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_TEXT, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
        $this->assertSame('single_text', $fieldDto->getFormTypeOption('widget'));
        $this->assertFalse($fieldDto->getFormTypeOption('html5'));
    }

    public function testFieldRenderAsNotText()
    {
        $field = TimeField::new('foo');
        $field->setFieldFqcn(TimeField::class);
        $field->renderAsText(false);
        $fieldDto = $this->configure($field);

        $this->assertSame(DateTimeField::WIDGET_NATIVE, $fieldDto->getCustomOption(TimeField::OPTION_WIDGET));
    }
}

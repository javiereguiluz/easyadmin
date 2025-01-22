<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\MoneyConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;

class MoneyFieldTest extends AbstractFieldTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->configurator = new MoneyConfigurator(new IntlFormatter(), static::getContainer()->get('property_accessor'));
    }

    public function testFieldWithoutCurrency()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = MoneyField::new('foo')->setValue(100);
        $this->configure($field);
    }

    public function testNullFieldWithoutCurrency()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = MoneyField::new('foo')->setValue(null);
        $this->configure($field);
    }

    public function testFieldWithNullValues()
    {
        $field = MoneyField::new('foo')->setValue(null)->setCurrency('EUR');
        $fieldDto = $this->configure($field);

        self::assertSame('EUR', $fieldDto->getCustomOption(MoneyField::OPTION_CURRENCY));
    }

    public function testFieldWithWrongCurrency()
    {
        $this->expectException(\InvalidArgumentException::class);

        $field = MoneyField::new('foo')->setValue(100)->setCurrency('THIS_DOES_NOT_EXIST');
        $this->configure($field);
    }

    public function testFieldWithHardcodedCurrency()
    {
        $field = MoneyField::new('foo')->setValue(100)->setCurrency('EUR');
        $fieldDto = $this->configure($field);

        self::assertSame('EUR', $fieldDto->getCustomOption(MoneyField::OPTION_CURRENCY));
        self::assertSame('EUR', $fieldDto->getFormTypeOption('currency'));
    }

    protected function getEntityDto(): EntityDto
    {
        $reflectedClass = new \ReflectionClass(EntityDto::class);
        $entityDto = $reflectedClass->newInstanceWithoutConstructor();
        $primaryKeyNameProperty = $reflectedClass->getProperty('primaryKeyName');
        $primaryKeyNameProperty->setValue($entityDto, 'id');
        $primaryKeyValueProperty = $reflectedClass->getProperty('primaryKeyValue');
        $primaryKeyValueProperty->setValue($entityDto, 1);
        $fqcnProperty = $reflectedClass->getProperty('fqcn');
        $fqcnProperty->setValue($entityDto, 'App\Entity\MyEntity');
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setValue($entityDto, new class {
            public int $id = 1;
            public string $bar = 'USD';
        });

        return $this->entityDto = $entityDto;
    }

    public function testFieldWithPropertyPathCurrency()
    {
        $field = MoneyField::new('foo')->setValue(100)->setCurrencyPropertyPath('bar');
        $fieldDto = $this->configure($field);

        self::assertSame('USD', $fieldDto->getFormTypeOption('currency'));
    }

    public function testFieldDecimals()
    {
        $field = MoneyField::new('foo')->setValue(100)->setCurrency('EUR');
        $fieldDto = $this->configure($field);
        self::assertSame(2, $fieldDto->getCustomOption('numDecimals'));
        self::assertSame(2, $fieldDto->getFormTypeOption('scale'));

        $field->setNumDecimals(3);
        $fieldDto = $this->configure($field);
        self::assertSame(3, $fieldDto->getCustomOption('numDecimals'));
        self::assertSame(3, $fieldDto->getFormTypeOption('scale'));
    }

    public function testFieldsDefaultsToCents()
    {
        $field = MoneyField::new('foo')->setValue(100)->setCurrency('EUR');
        $fieldDto = $this->configure($field);
        self::assertSame('€1.00', $fieldDto->getFormattedValue());
        self::assertSame(100, $fieldDto->getFormTypeOption('divisor'));
    }

    public function testFieldCents()
    {
        $field = MoneyField::new('foo')->setValue(100)->setCurrency('EUR');
        $field->setStoredAsCents(false);
        $fieldDto = $this->configure($field);
        self::assertSame('€100.00', $fieldDto->getFormattedValue());
        self::assertSame(1, $fieldDto->getFormTypeOption('divisor'));
    }

    public function testFieldWithCustomDivisor()
    {
        $field = MoneyField::new('foo')->setValue(725)->setCurrency('EUR');
        $field->setFormTypeOption('divisor', 10000);
        $fieldDto = $this->configure($field);
        self::assertSame('€0.07', $fieldDto->getFormattedValue());
        self::assertSame(10000, $fieldDto->getFormTypeOption('divisor'));
    }
}

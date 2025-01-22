<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\Configurator\PercentConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Intl\IntlFormatter;

class PercentFieldTest extends AbstractFieldTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->configurator = new PercentConfigurator(new IntlFormatter());
    }

    public function testFieldWithNullValues()
    {
        $field = PercentField::new('foo')->setValue(null);
        $fieldDto = $this->configure($field);

        self::assertNull($fieldDto->getFormattedValue());
        self::assertSame('%', $fieldDto->getCustomOption(PercentField::OPTION_SYMBOL));
    }

    public function testFieldDefaultDecimalsAndFractional()
    {
        $field = PercentField::new('foo')->setValue(100.9874)->setStoredAsFractional(false);
        $fieldDto = $this->configure($field);
        self::assertSame(0, $fieldDto->getCustomOption(PercentField::OPTION_NUM_DECIMALS));
        self::assertSame(0, $fieldDto->getFormTypeOption('scale'));
        self::assertSame('101%', $fieldDto->getFormattedValue());
    }

    public function testFieldDecimalsAndFractional()
    {
        $field = PercentField::new('foo')->setValue(100.1345)->setStoredAsFractional(false)->setNumDecimals(3);
        $fieldDto = $this->configure($field);
        self::assertSame(3, $fieldDto->getCustomOption(PercentField::OPTION_NUM_DECIMALS));
        self::assertSame(3, $fieldDto->getFormTypeOption('scale'));
        self::assertSame('100.135%', $fieldDto->getFormattedValue());
    }

    public function testFieldSynmbolAndFractional()
    {
        $field = PercentField::new('foo')->setValue(100)->setSymbol(' %')->setStoredAsFractional(false);
        $fieldDto = $this->configure($field);
        self::assertSame('100 %', $fieldDto->getFormattedValue());
        self::assertSame('integer', $fieldDto->getFormTypeOption('type'));
    }
}

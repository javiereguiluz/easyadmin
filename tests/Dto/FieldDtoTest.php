<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use PHPUnit\Framework\TestCase;

class FieldDtoTest extends TestCase
{
    /** @dataProvider propertyNameWithSuffixProvider */
    public function testGetPropertyNameWithSuffix(string $property, ?string $propertySuffix, string $expected): void
    {
        $dto = new FieldDto();
        $dto->setProperty($property);
        $dto->setPropertyNameSuffix($propertySuffix);

        $this->assertSame($expected, $dto->getPropertyNameWithSuffix());
    }

    public function propertyNameWithSuffixProvider(): \Generator
    {
        yield ['foo', null, 'foo'];
        yield ['foo', 'bar', 'foo_bar'];
    }
}

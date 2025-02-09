<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Type;

use Doctrine\ORM\Mapping\ClassMetadata;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FormLayoutFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class CrudFormTypeFormFormFieldTest extends TypeTestCase
{
    /** @dataProvider formFieldFixedProvider */
    public function testFormFieldFixed(FormField $field, array $expectedKeys)
    {
        $form = $this->factory->create(CrudFormType::class, null, [
            'entityDto' => $this->getEntityDto([$field]),
        ]);

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $form);
        }
    }

    public function formFieldFixedProvider(): iterable
    {
        yield [FormField::addFieldset(propertySuffix: 'foobar'), ['ea_form_fieldset_foobar', 'ea_form_fieldset_close_foobar']];
        yield [FormField::addRow(propertySuffix: 'foobar'), ['ea_form_row_foobar']];
        yield [FormField::addColumn(propertySuffix: 'foobar'), ['ea_form_column_foobar', 'ea_form_column_close_foobar']];
        yield [FormField::addTab(propertySuffix: 'foobar'), ['ea_form_tab_foobar']];
    }

    /** @dataProvider formFieldUlidProvider */
    public function testFormFieldUlid(FormField $field, array $expectedPrefixKeys, string $expectedSuffix): void
    {
        $form = $this->factory->create(CrudFormType::class, null, [
            'entityDto' => $this->getEntityDto([$field]),
        ]);

        foreach ($expectedPrefixKeys as $prefixKey) {
            $this->assertArrayHasKey($prefixKey.$expectedSuffix, $form);
        }
    }

    public function formFieldUlidProvider(): iterable
    {
        yield [$field = FormField::addFieldset(), ['ea_form_fieldset_', 'ea_form_fieldset_close_'], $field->getAsDto()->getPropertyNameSuffix()];
        yield [$field = FormField::addRow(), ['ea_form_row_'], $field->getAsDto()->getPropertyNameSuffix()];
        yield [$field = FormField::addColumn(), ['ea_form_column_', 'ea_form_column_close_'], $field->getAsDto()->getPropertyNameSuffix()];
        yield [$field = FormField::addTab(), ['ea_form_tab_'], $field->getAsDto()->getPropertyNameSuffix()];
    }

    protected function getExtensions(): array
    {
        $typeGuesser = $this->getMockBuilder(DoctrineOrmTypeGuesser::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $types = [
            'ea_crud' => new CrudFormType($typeGuesser),
        ];

        return [
            new PreloadedExtension($types, []),
        ];
    }

    private function getEntityDto(array $fields): EntityDto
    {
        $classMetadataMock = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();
        $classMetadataMock->method('getIdentifierFieldNames')->willReturn(['id']);

        $entityDto = new EntityDto(_TestEntity::class, $classMetadataMock);
        $reflected = new \ReflectionClass($entityDto);
        $fieldsProperty = $reflected->getProperty('fields');
        $fieldsProperty->setValue($entityDto, (new FormLayoutFactory())->createLayout(FieldCollection::new($fields), Crud::PAGE_NEW));

        return $entityDto;
    }
}

class _TestEntity
{
}

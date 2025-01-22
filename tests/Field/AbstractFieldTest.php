<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use Doctrine\ORM\Mapping\ClassMetadata;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextDirection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Context\AdminContextInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\I18nDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Registry\TemplateRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFieldTest extends KernelTestCase
{
    protected $entityDto;
    protected $adminContext;
    protected $configurator;

    protected function getEntityDto(): EntityDto
    {
        $reflectedClass = new \ReflectionClass(ClassMetadata::class);
        $classMetadata = $reflectedClass->newInstanceWithoutConstructor();

        $reflectedClass = new \ReflectionClass(EntityDto::class);
        $entityDto = $reflectedClass->newInstanceWithoutConstructor();
        $instanceProperty = $reflectedClass->getProperty('instance');
        $instanceProperty->setValue($entityDto, new class {});
        $metadataProperty = $reflectedClass->getProperty('metadata');
        $metadataProperty->setValue($entityDto, $classMetadata);

        return $this->entityDto = $entityDto;
    }

    private function getAdminContext(string $pageName, string $requestLocale, string $actionName): AdminContextInterface
    {
        self::bootKernel();

        $crudDto = new CrudDto();
        $crudDto->setPageName($pageName);
        $crudDto->setCurrentAction($actionName);
        $crudDto->setDatePattern(DateTimeField::FORMAT_MEDIUM);
        $crudDto->setTimePattern(DateTimeField::FORMAT_MEDIUM);
        $crudDto->setDateTimePattern(DateTimeField::FORMAT_MEDIUM, DateTimeField::FORMAT_MEDIUM);

        $i18Dto = new I18nDto($requestLocale, TextDirection::LTR, 'messages', []);

        $reflectedClass = new \ReflectionClass(Request::class);
        $request = $reflectedClass->newInstanceWithoutConstructor();
        $instanceProperty = $reflectedClass->getProperty('locale');
        $instanceProperty->setValue($request, $requestLocale);

        $reflectedClass = new \ReflectionClass(TemplateRegistry::class);
        $templateRegistry = $reflectedClass->newInstanceWithoutConstructor();

        $reflectedClass = new \ReflectionClass(AdminContext::class);
        $adminContext = $reflectedClass->newInstanceWithoutConstructor();
        $requestProperty = $reflectedClass->getProperty('request');
        $requestProperty->setValue($adminContext, $request);
        $crudDtoProperty = $reflectedClass->getProperty('crudDto');
        $crudDtoProperty->setValue($adminContext, $crudDto);
        $i18nDtoProperty = $reflectedClass->getProperty('i18nDto');
        $i18nDtoProperty->setValue($adminContext, $i18Dto);
        $templateRegistryProperty = $reflectedClass->getProperty('templateRegistry');
        $templateRegistryProperty->setValue($adminContext, $templateRegistry);

        return $this->adminContext = $adminContext;
    }

    protected function configure(FieldInterface $field, string $pageName = Crud::PAGE_INDEX, string $requestLocale = 'en', string $actionName = Action::INDEX): FieldDto
    {
        $fieldDto = $field->getAsDto();
        $this->configurator->configure($fieldDto, $this->getEntityDto(), $this->getAdminContext($pageName, $requestLocale, $actionName));

        return $fieldDto;
    }
}

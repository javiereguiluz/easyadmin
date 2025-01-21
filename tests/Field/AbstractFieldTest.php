<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\I18nDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFieldTest extends KernelTestCase
{
    protected $entityDto;
    protected $adminContext;
    protected $configurator;

    private function getEntityDto(): EntityDto
    {
        $entityDtoMock = $this->createMock(EntityDto::class);
        $entityDtoMock
            ->expects($this->any())
            ->method('getInstance')
            ->willReturn(new class {});

        return $this->entityDto = $entityDtoMock;
    }

    private function getAdminContext(string $pageName, string $requestLocale, string $actionName): AdminContext
    {
        self::bootKernel();

        $crudMock = $this->getMockBuilder(CrudDto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCurrentPage', 'getCurrentAction', 'getDatePattern', 'getDateTimePattern', 'getTimePattern'])
            ->getMock();
        $crudMock->method('getCurrentPage')->willReturn($pageName);
        $crudMock->method('getCurrentAction')->willReturn($actionName);
        $crudMock->method('getDatePattern')->willReturn(DateTimeField::FORMAT_MEDIUM);
        $crudMock->method('getTimePattern')->willReturn(DateTimeField::FORMAT_MEDIUM);
        $crudMock->method('getDateTimePattern')->willReturn([DateTimeField::FORMAT_MEDIUM, DateTimeField::FORMAT_MEDIUM]);

        $i18nMock = $this->getMockBuilder(I18nDto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTranslationParameters', 'getTranslationDomain'])
            ->getMock();
        $i18nMock->method('getTranslationParameters')->willReturn([]);
        $i18nMock->method('getTranslationDomain')->willReturn('messages');

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLocale'])
            ->getMock();
        $requestMock->method('getLocale')->willReturn($requestLocale);

        $adminContextMock = $this->getMockBuilder(AdminContext::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRequest', 'getCrud', 'getI18n', 'getTemplatePath'])
            ->getMock();
        $adminContextMock
            ->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);
        $adminContextMock
            ->expects($this->any())
            ->method('getCrud')
            ->willReturn($crudMock);
        $adminContextMock
            ->expects($this->any())
            ->method('getI18n')
            ->willReturn($i18nMock);
        $adminContextMock
            ->expects($this->any())
            ->method('getTemplatePath')
            ->willReturn('@EasyAdmin/layout.html.twig'); // return any path to avoid injecting a TemplateRegistry

        return $this->adminContext = $adminContextMock;
    }

    protected function configure(FieldInterface $field, string $pageName = Crud::PAGE_INDEX, string $requestLocale = 'en', string $actionName = Action::INDEX): FieldDto
    {
        $fieldDto = $field->getAsDto();
        $this->configurator->configure($fieldDto, $this->getEntityDto(), $this->getAdminContext($pageName, $requestLocale, $actionName));

        return $fieldDto;
    }
}

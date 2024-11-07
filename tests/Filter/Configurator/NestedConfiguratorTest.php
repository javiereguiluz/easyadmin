<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\Configurator\NestedConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NestedFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NestedConfiguratorTest extends KernelTestCase
{
    /** @var Registry */
    private $doctrine;

    /** @var NestedConfigurator */
    private $nestedConfigurator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->doctrine = $container->get('doctrine');
        $this->nestedConfigurator = $container->get(NestedConfigurator::class);
    }

    public function testConfigure()
    {
        $class = User::class;
        $attr = ['class' => 'foo'];

        $textFilter = TextFilter::new('blogPosts.categories.name');
        $textFilter->setFormTypeOption('attr', $attr);

        $nestedFilter = NestedFilter::wrap($textFilter);

        $objectManager = $this->doctrine->getManagerForClass($class);
        $entityDto = new EntityDto($class, $objectManager->getClassMetadata($class));
        $adminContext = $this->getMockBuilder(AdminContext::class)->disableOriginalConstructor()->getMock();

        $wrappedFilter = $nestedFilter->getWrappedFilter();
        $wrappedFilterDto = $wrappedFilter->getAsDto();
        $nestedFilterDto = $nestedFilter->getAsDto();

        self::assertEquals('blogPosts.categories.name', $wrappedFilterDto->getProperty());

        $this->nestedConfigurator->configure($nestedFilter->getAsDto(), null, $entityDto, $adminContext);

        self::assertEquals('name', $wrappedFilterDto->getProperty());
        self::assertEquals('blogPosts_categories_name', $nestedFilterDto->getProperty());

        self::assertEquals($wrappedFilterDto->getFormType(), $nestedFilterDto->getFormType());
        self::assertEquals($wrappedFilterDto->getFormTypeOptions(), $nestedFilterDto->getFormTypeOptions());
    }
}

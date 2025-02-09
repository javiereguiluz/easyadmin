<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Filter;

use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\FilterRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Guesser\DoctrineOrmFilterTypeGuesser;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Filter\Fixtures\FoobarFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Filter\Fixtures\FooFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Filter\Fixtures\InvalidFilterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserChain;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class FilterRegistryTest extends TestCase
{
    /** @var FilterRegistry */
    private $filterRegistry;

    protected function setUp(): void
    {
        $typesMap = [
            'foo' => 'easyadmin.filter.type.foo',
        ];
        $typeGuesser = $this->getMockBuilder(DoctrineOrmFilterTypeGuesser::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->filterRegistry = new FilterRegistry($typesMap, [$typeGuesser]);
    }

    public function testHasType()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $this->assertTrue($this->filterRegistry->hasType('foo'));
        $this->assertFalse($this->filterRegistry->hasType('bar'));
    }

    public function testGetType()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $this->assertSame('easyadmin.filter.type.foo', $this->filterRegistry->getType('foo'));
    }

    public function testGetInvalidType()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $this->setExpectedException(\Symfony\Component\Form\Exception\InvalidArgumentException::class);

        $this->filterRegistry->getType('bar');
    }

    public function testGetTypeGuesser()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $typeGuesser = $this->filterRegistry->getTypeGuesser();

        $this->assertInstanceOf(FormTypeGuesserChain::class, $typeGuesser);
    }

    public function testResolveType()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $filterType = new FooFilterType();
        $form = $this->createFilterForm($filterType);

        $this->assertSame($filterType, $this->filterRegistry->resolveType($form));
    }

    public function testResolveTypeThroughParents()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $fooFilterType = new FooFilterType();
        $foobarFilterType = new FoobarFilterType();
        $form = $this->createFilterForm($fooFilterType, $foobarFilterType);

        $this->assertSame($fooFilterType, $this->filterRegistry->resolveType($form));
    }

    public function testInvalidFilterType()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        $this->setExpectedException(\Symfony\Component\Form\Exception\RuntimeException::class, 'Filter type "EasyCorp\\TestBundle\\EasyAdminBundle\\Tests\\Form\\Filter\\Fixtures\\InvalidFilterType" must implement "EasyCorp\\TestBundle\\EasyAdminBundle\\Form\\Filter\\Type\\FilterInterface".');

        $filterType = new InvalidFilterType();
        $form = $this->createFilterForm($filterType);

        $this->filterRegistry->resolveType($form);
    }

    private function createFilterForm(FormTypeInterface $filterType, ?FormTypeInterface $childFilterType = null)
    {
        $resolvedFormType = $this->getMockBuilder(ResolvedFormTypeInterface::class)->getMock();
        $resolvedFormType->method('getInnerType')->willReturn($filterType);

        if ($childFilterType) {
            $childResolvedFormType = $this->getMockBuilder(ResolvedFormTypeInterface::class)->getMock();
            $childResolvedFormType->method('getInnerType')->willReturn($childFilterType);
            $childResolvedFormType->method('getParent')->willReturn($resolvedFormType);

            $childFormConfig = $this->getMockBuilder(FormConfigInterface::class)->getMock();
            $childFormConfig->method('getType')->willReturn($childResolvedFormType);

            $childForm = $this->getMockBuilder(FormInterface::class)->getMock();
            $childForm->method('getConfig')->willReturn($childFormConfig);

            return $childForm;
        }

        $formConfig = $this->getMockBuilder(FormConfigInterface::class)->getMock();
        $formConfig->method('getType')->willReturn($resolvedFormType);

        $form = $this->getMockBuilder(FormInterface::class)->getMock();
        $form->method('getConfig')->willReturn($formConfig);

        return $form;
    }
}

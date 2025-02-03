<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Form\ChoiceList\Loader;

use EasyCorp\Bundle\EasyAdminBundle\Form\ChoiceList\Loader\DynamicChoiceLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class DynamicChoiceLoaderTest extends TestCase
{
    /** @var ChoiceLoaderInterface */
    private $loader;

    protected function setUp(): void
    {
        $this->loader = new DynamicChoiceLoader();
    }

    public function testLoadChoicesFromValues()
    {
        $this->assertSame(['foo'], $this->loader->loadChoicesForValues(['foo']));
        $this->assertSame(['bar'], $this->loader->loadChoicesForValues(['bar']));
    }

    public function testLoadValuesFromChoices()
    {
        $this->assertSame(['foo'], $this->loader->loadValuesForChoices(['foo']));
        $this->assertSame(['bar'], $this->loader->loadValuesForChoices(['bar']));
    }

    public function testChoiceListIsBuiltFromValues()
    {
        $this->assertSame(['foo'], $this->loader->loadChoicesForValues(['foo']));
        $this->assertSame(['foo' => 'foo'], $this->loader->loadChoiceList()->getChoices());

        $this->assertSame(['bar'], $this->loader->loadChoicesForValues(['bar']));
        $this->assertSame(['bar' => 'bar'], $this->loader->loadChoiceList()->getChoices());
    }

    public function testCachedChoiceList()
    {
        $choiceList = $this->loader->loadChoiceList();

        $this->assertInstanceOf(ArrayChoiceList::class, $choiceList);
        $this->assertSame([], $choiceList->getChoices());
        $this->assertSame($choiceList, $this->loader->loadChoiceList());
    }
}

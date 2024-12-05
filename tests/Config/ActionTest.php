<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use function Symfony\Component\Translation\t;

class ActionTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testDeprecatedActionLabels()
    {
        $this->expectDeprecation('Since easycorp/easyadmin-bundle 4.0.5: Argument "$label" for "EasyCorp\Bundle\EasyAdminBundle\Config\Action::new" must be one of these types: "Symfony\Contracts\Translation\TranslatableInterface", "string", "callable", "false" or "null". Passing type "integer" will cause an error in 5.0.0.');

        Action::new(Action::EDIT, 7);
    }

    /**
     * @dataProvider provideAutomaticActionLabels
     */
    public function testActionWithAutomaticLabel(string $actionName, string $automaticLabel)
    {
        $actionConfig = Action::new($actionName)->linkToCrudAction('');

        $this->assertSame($automaticLabel, $actionConfig->getAsDto()->getLabel());
    }

    /**
     * @dataProvider provideActionLabels
     */
    public function testAllPossibleValuesForActionLabels($label)
    {
        $actionConfig = Action::new(Action::EDIT, $label)->linkToCrudAction('');

        $this->assertSame($label, $actionConfig->getAsDto()->getLabel());
    }

    public function testCallableLabelForDynamicLabelGeneration()
    {
        $callable = static function (object $entity) {
            return sprintf('Delete %s', $entity);
        };

        $actionConfig = Action::new(Action::DELETE)
            ->setLabel($callable)
            ->linkToCrudAction('');

        $dto = $actionConfig->getAsDto();

        $this->assertSame($callable, $dto->getLabel());
    }

    public function testDefaultCssClass()
    {
        $actionConfig = Action::new(Action::DELETE)->linkToCrudAction('');

        $this->assertSame('', $actionConfig->getAsDto()->getCssClass());
        $this->assertSame('', $actionConfig->getAsDto()->getAddedCssClass());
    }

    public function testSetCssClass()
    {
        $actionConfig = Action::new(Action::DELETE)->linkToCrudAction('')
            ->setCssClass('foo');

        $this->assertSame('foo', $actionConfig->getAsDto()->getCssClass());
        $this->assertSame('', $actionConfig->getAsDto()->getAddedCssClass());
    }

    public function testAddCssClass()
    {
        $actionConfig = Action::new(Action::DELETE)->linkToCrudAction('')
            ->addCssClass('foo');

        $this->assertSame('', $actionConfig->getAsDto()->getCssClass());
        $this->assertSame('foo', $actionConfig->getAsDto()->getAddedCssClass());
    }

    public function testSetAndAddCssClass()
    {
        $actionConfig = Action::new(Action::DELETE)->linkToCrudAction('')
            ->setCssClass('foo')->addCssClass('bar');

        $this->assertSame('foo', $actionConfig->getAsDto()->getCssClass());
        $this->assertSame('bar', $actionConfig->getAsDto()->getAddedCssClass());
    }

    public function testSetAndAddCssClassWithSpaces()
    {
        $actionConfig = Action::new(Action::DELETE)->linkToCrudAction('')
            ->setCssClass('      foo1   foo2  ')->addCssClass('     bar1    bar2   ');

        $this->assertSame('foo1   foo2', $actionConfig->getAsDto()->getCssClass());
        $this->assertSame('bar1    bar2', $actionConfig->getAsDto()->getAddedCssClass());
    }

    public function provideAutomaticActionLabels(): iterable
    {
        // format: (action name, automatic label generated for the action)
        yield ['Edit', 'Edit'];
        yield ['FooBar', 'Foo Bar'];
        yield ['fooBar', 'Foo Bar'];
        yield ['foo_Bar', 'Foo Bar'];
    }

    public function provideActionLabels(): iterable
    {
        yield [false];
        yield [''];
        yield ['Edit'];
        yield [fn (object $entity) => sprintf('Edit %s', $entity)];
        yield [static function (object $entity) {
            return sprintf('Edit %s', $entity);
        }];
        yield [t('Edit')];
    }
}

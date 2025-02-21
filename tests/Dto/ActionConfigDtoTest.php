<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDto;
use PHPUnit\Framework\TestCase;

final class ActionConfigDtoTest extends TestCase
{
    public static function provideLabels(): \Generator
    {
        yield 'nothing to enable' => [[], ['foo', 'bar', 'baz', 'qux']];
        yield 'enable action not disabled' => [['not-disabled'], ['foo', 'bar', 'baz', 'qux']];
        yield 'enable 1 action' => [['bar'], ['foo', 'baz', 'qux']];
        yield 'enable multiple action' => [['foo', 'baz'], ['bar', 'qux']];
        yield 'enable all' => [['foo', 'bar', 'baz', 'qux'], []];
    }

    /**
     * @dataProvider provideLabels
     */
    public function testEnableActions(array $actionsToEnable, array $expectedDisabledActions): void
    {
        $actionConfigDto = new ActionConfigDto();
        $actionConfigDto->disableActions(['foo', 'bar', 'baz', 'qux']);

        $this->assertSame(['foo', 'bar', 'baz', 'qux'], $actionConfigDto->getDisabledActions());

        $actionConfigDto->enableActions($actionsToEnable);
        $this->assertSame($expectedDisabledActions, $actionConfigDto->getDisabledActions());
    }
}

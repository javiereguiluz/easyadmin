<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    public function testEnableDeleteAction(): void
    {
        $actions = Actions::new();
        $actions->disable(Action::DELETE);

        $this->assertSame(['delete', 'batchDelete'], $actions->getAsDto(null)->getDisabledActions());

        $actions->enable(Action::DELETE);
        $this->assertSame([], $actions->getAsDto(null)->getDisabledActions());
    }

    public function testEnableBatchDeleteAction(): void
    {
        $actions = Actions::new();
        $actions->disable(Action::BATCH_DELETE);

        $this->assertSame(['batchDelete'], $actions->getAsDto(null)->getDisabledActions());

        $actions->enable(Action::BATCH_DELETE);
        $this->assertSame([], $actions->getAsDto(null)->getDisabledActions());
    }

    public function testEnableBatchDeleteActionWillEnableDeleteAsWell(): void
    {
        $actions = Actions::new();
        $actions->disable(Action::DELETE, Action::BATCH_DELETE);

        $this->assertSame(['delete', 'batchDelete'], $actions->getAsDto(null)->getDisabledActions());

        $actions->enable(Action::BATCH_DELETE);
        $this->assertSame([], $actions->getAsDto(null)->getDisabledActions());
    }
}

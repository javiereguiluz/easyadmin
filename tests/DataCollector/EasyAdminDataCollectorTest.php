<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\DataCollector;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EasyAdminDataCollectorTest extends WebTestCase
{
    protected static $options = ['environment' => 'default_backend'];

    public function testCollectorIsEnabled()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        static::$client->enableProfiler();
        $this->requestListView();

        $this->assertNotNull(static::$client->getProfile()->getCollector('easyadmin'));
    }

    public function testCollectorInformationForListView()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        static::$client->enableProfiler();
        $this->requestListView();
        $collector = static::$client->getProfile()->getCollector('easyadmin');

        $this->assertSame(5, $collector->getNumEntities());

        $parameters = [
            'action' => 'list',
            'entity' => 'Category',
            'id' => null,
            'sort_field' => 'id',
            'sort_direction' => 'DESC',
        ];
        $this->assertSame($parameters, $collector->getRequestParameters());

        $currentConfiguration = $collector->getCurrentEntityConfig();
        $this->assertSame('Category', $currentConfiguration['name']);

        $backendConfig = $collector->getBackendConfig();
        $this->assertCount(5, $backendConfig['entities']);
    }

    public function testCollectorInformationForEditView()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        static::$client->enableProfiler();
        $this->requestEditView();
        $collector = static::$client->getProfile()->getCollector('easyadmin');

        $this->assertSame(5, $collector->getNumEntities());

        $parameters = [
            'action' => 'edit',
            'entity' => 'Category',
            'id' => '200',
            'sort_field' => 'id',
            'sort_direction' => 'DESC',
        ];
        $this->assertSame($parameters, $collector->getRequestParameters());

        $currentConfiguration = $collector->getCurrentEntityConfig();
        $this->assertSame('Category', $currentConfiguration['name']);

        $backendConfig = $collector->getBackendConfig();
        $this->assertCount(5, $backendConfig['entities']);
    }

    public function testCollectorReset()
    {
        $this->markTestSkipped('This test should be updated or deleted...');

        static::$client->enableProfiler();
        $this->requestListView();
        $collector = static::$client->getProfile()->getCollector('easyadmin');

        $this->assertSame(5, $collector->getNumEntities());
        $collector->reset();
        $this->assertSame(0, $collector->getNumEntities());
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\DataCollector;

use EasyCorp\Bundle\EasyAdminBundle\Inspector\DataCollector;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EasyAdminDataCollectorTest extends WebTestCase
{
    public function testCollectorIsEnabled(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->enableProfiler();

        $client->request('GET', '/admin');
        $this->assertNotNull($client->getProfile()->getCollector('easyadmin'));
    }

    public function testCollectorData(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->enableProfiler();

        $client->request('GET', '/admin/category');
        /** @var DataCollector $collector */
        $collector = $client->getProfile()->getCollector('easyadmin');
        $collectedData = $collector->getData();

        $this->assertSame(['CRUD Controller FQCN', 'CRUD Action', 'Entity ID', 'Sort'], array_keys($collectedData));
        $this->assertTrue($collector->isEasyAdminRequest());
    }
}

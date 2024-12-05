<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller\Search;

use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Search\IdTermCrudSearchController;

class IdTermCrudSearchControllerTest extends AbstractCrudTestCase
{
    protected function getControllerFqcn(): string
    {
        return IdTermCrudSearchController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
    }

    /**
     * @dataProvider provideSearchTests
     */
    public function testSearch(string $query, int $expectedResultCount)
    {
        $this->client->request('GET', $this->generateIndexUrl($query));
        static::assertResponseIsSuccessful();
        static::assertIndexFullEntityCount($expectedResultCount);
    }

    public static function provideSearchTests(): iterable
    {
        // the CRUD Controller associated to this test has configured the search
        // properties used by the search engine. That's why results are not the default ones
        yield 'search by non numeric query yields no results' => [
            'blog post',
            0,
        ];

        yield 'search by id yield 1 result' => [
            '15',
            1,
        ];
    }
}

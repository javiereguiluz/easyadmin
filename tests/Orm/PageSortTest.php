<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Orm;

use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Sort\PageCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Page;

class PageSortTest extends AbstractCrudTestCase
{
    private $repository;

    protected function getControllerFqcn(): string
    {
        return PageCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->repository = $this->entityManager->getRepository(Page::class);
    }

    /**
     * @dataProvider sorting
     */
    public function testSorting(array $query, ?string $sortFunction, string $expectedSortIcon)
    {
        // Arrange
        $expectedAmountMapping = [];

        /**
         * @var Page $entity
         */
        foreach ($this->repository->findAll() as $entity) {
            $expectedAmountMapping[$entity->getName()] = $entity->getWebsite()->getName();
        }

        if (null !== $sortFunction) {
            $sortFunction($expectedAmountMapping);
        }

        // Act
        $crawler = $this->client->request('GET', $this->generateIndexUrl().'&'.http_build_query($query));

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('th.header-for-field-association > a', 'Website');
        $this->assertSelectorExists('th.header-for-field-association span.icon svg');

        $index = 1;

        foreach ($expectedAmountMapping as $expectedPageName => $expectedWebsiteName) {
            $expectedRow = $index++;

            $this->assertSelectorTextSame('tbody tr:nth-child('.$expectedRow.') td:nth-child(2)', $expectedPageName, sprintf('Expected "%s" in row %d', $expectedPageName, $expectedRow));
            $this->assertSelectorTextSame('tbody tr:nth-child('.$expectedRow.') td:nth-child(3)', $expectedWebsiteName, sprintf('Expected "%s" in row %d', $expectedWebsiteName, $expectedRow));
        }
    }

    public function sorting(): \Generator
    {
        yield [
            [],
            null,
            'internal:sort-arrows',
        ];

        yield [
            ['sort' => ['website' => 'ASC']],
            'asort',
            'internal:sort-arrow-up',
        ];

        yield [
            ['sort' => ['website' => 'DESC']],
            'arsort',
            'internal:sort-arrow-down',
        ];
    }
}

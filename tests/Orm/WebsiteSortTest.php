<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Orm;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Sort\WebsiteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Website;

class WebsiteSortTest extends AbstractCrudTestCase
{
    /**
     * @var EntityRepository
     */
    private $repository;

    protected function getControllerFqcn(): string
    {
        return WebsiteCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->repository = $this->entityManager->getRepository(Website::class);
    }

    /**
     * @dataProvider sorting
     */
    public function testSorting(array $query, ?\Closure $sortFunction, string $expectedSortIcon)
    {
        // Arrange
        $expectedAmountMapping = [];
        $entities = $this->repository->findAll();

        if (null !== $sortFunction) {
            $sortFunction($entities);
        }

        /**
         * @var Website $entity
         */
        foreach ($entities as $entity) {
            $expectedAmountMapping[$entity->getName()] = $entity->getPages()->count();
        }

        // Act
        $crawler = $this->client->request('GET', $this->generateIndexUrl().'&'.http_build_query($query));

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('th.header-for-field-association > a', 'Pages');
        $this->assertSelectorExists('th.header-for-field-association span.icon svg');

        $index = 1;

        foreach ($expectedAmountMapping as $expectedName => $expectedValue) {
            $expectedRow = $index++;

            $this->assertSelectorTextSame('tbody tr:nth-child('.$expectedRow.') td:nth-child(2)', $expectedName, sprintf('Expected "%s" in row %d', $expectedName, $expectedRow));
            $this->assertSelectorTextSame('tbody tr:nth-child('.$expectedRow.') td:nth-child(3)', $expectedValue, sprintf('Expected "%s" in row %d', $expectedValue, $expectedRow));
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
            ['sort' => ['pages' => 'ASC']],
            /**
             * @param list<Website> $array
             */
            function (array &$array) {
                usort($array, static function (Website $a, Website $b) {
                    $aPages = $a->getPages()->count();
                    $bPages = $b->getPages()->count();

                    if ($aPages === $bPages) {
                        return $a->getId() <=> $b->getId();
                    }

                    return $aPages <=> $bPages;
                });
            },
            'internal:sort-arrow-up',
        ];

        yield [
            ['sort' => ['pages' => 'DESC']],
            /**
             * @param list<Website> $array
             */
            function (array &$array) {
                usort($array, static function (Website $a, Website $b) {
                    $aPages = $a->getPages()->count();
                    $bPages = $b->getPages()->count();

                    if ($aPages === $bPages) {
                        return $b->getId() <=> $a->getId();
                    }

                    return $bPages <=> $aPages;
                });
            },
            'internal:sort-arrow-down',
        ];
    }
}

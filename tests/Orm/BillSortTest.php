<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Orm;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Sort\BillCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Bill;

class BillSortTest extends AbstractCrudTestCase
{
    /**
     * @var EntityRepository
     */
    private $repository;

    protected function getControllerFqcn(): string
    {
        return BillCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->repository = $this->entityManager->getRepository(Bill::class);
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
         * @var Bill $entity
         */
        foreach ($entities as $entity) {
            $expectedAmountMapping[$entity->getName()] = $entity->getCustomers()->count();
        }

        // Act
        $crawler = $this->client->request('GET', $this->generateIndexUrl().'&'.http_build_query($query));

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('th.header-for-field-association > a', 'Customers');
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
            ['sort' => ['customers' => 'ASC']],
            /**
             * @param list<Bill> $array
             */
            function (array &$array) {
                usort($array, static function (Bill $a, Bill $b) {
                    $aCustomers = $a->getCustomers()->count();
                    $bCustomers = $b->getCustomers()->count();

                    if ($aCustomers === $bCustomers) {
                        return $a->getId() <=> $b->getId();
                    }

                    return $aCustomers <=> $bCustomers;
                });
            },
            'internal:sort-arrow-up',
        ];

        yield [
            ['sort' => ['customers' => 'DESC']],
            /**
             * @param list<Bill> $array
             */
            function (array &$array) {
                usort($array, static function (Bill $a, Bill $b) {
                    $aCustomers = $a->getCustomers()->count();
                    $bCustomers = $b->getCustomers()->count();

                    if ($aCustomers === $bCustomers) {
                        return $b->getId() <=> $a->getId();
                    }

                    return $bCustomers <=> $aCustomers;
                });
            },
            'internal:sort-arrow-down',
        ];
    }
}

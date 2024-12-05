<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Orm;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Sort\CustomerCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Customer;

class CustomerSortTest extends AbstractCrudTestCase
{
    /**
     * @var EntityRepository
     */
    private $repository;

    protected function getControllerFqcn(): string
    {
        return CustomerCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return DashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->repository = $this->entityManager->getRepository(Customer::class);
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
         * @var Customer $entity
         */
        foreach ($entities as $entity) {
            $expectedAmountMapping[$entity->getName()] = $entity->getBills()->count();
        }

        // Act
        $crawler = $this->client->request('GET', $this->generateIndexUrl().'&'.http_build_query($query));

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('th.header-for-field-association > a', 'Bills');
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
            ['sort' => ['bills' => 'ASC']],
            /**
             * @param list<Customer> $array
             */
            function (array &$array) {
                usort($array, static function (Customer $a, Customer $b) {
                    $aBills = $a->getBills()->count();
                    $bBills = $b->getBills()->count();

                    if ($aBills === $bBills) {
                        return $a->getId() <=> $b->getId();
                    }

                    return $aBills <=> $bBills;
                });
            },
            'internal:sort-arrow-up',
        ];

        yield [
            ['sort' => ['bills' => 'DESC']],
            /**
             * @param list<Customer> $array
             */
            function (array &$array) {
                usort($array, static function (Customer $a, Customer $b) {
                    $aBills = $a->getBills()->count();
                    $bBills = $b->getBills()->count();

                    if ($aBills === $bBills) {
                        return $b->getId() <=> $a->getId();
                    }

                    return $bBills <=> $aBills;
                });
            },
            'internal:sort-arrow-down',
        ];
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Filter\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\EntityFilterType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Bridge\Doctrine\Tests\Fixtures\SingleIntIdEntity;

class EntityFilterTypeTest extends FilterTypeTest
{
    protected const FILTER_TYPE = EntityFilterType::class;

    /** @var EntityManager */
    private $em;
    /** @var ManagerRegistry */
    private $emRegistry;

    protected function setUp(): void
    {
        $this->em = DoctrineTestHelper::createTestEntityManager();
        $this->emRegistry = $this->createRegistryMock('default', $this->em);

        parent::setUp();

        $schemaTool = new SchemaTool($this->em);
        $classes = [
            $this->em->getClassMetadata(SingleIntIdEntity::class),
        ];

        try {
            $schemaTool->dropSchema($classes);
        } catch (\Exception $e) {
        }

        try {
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em = null;
        $this->emRegistry = null;
    }

    /**
     * @dataProvider getDataProviderToOneAssoc
     */
    public function testSubmitAndFilterToOneAssociationType($submittedData, $data, array $options, string $dql, array $params)
    {
        $entity1 = new SingleIntIdEntity(1, 'Foo');
        $entity2 = new SingleIntIdEntity(2, 'Bar');
        $this->persist([$entity1, $entity2]);

        $form = $this->factory->create(static::FILTER_TYPE, null, $options);
        $form->submit($submittedData);
        $this->assertEquals($data, $form->getData());
        $this->assertEmpty($form->getExtraData());
        $this->assertTrue($form->isSynchronized());

        $filter = $this->filterRegistry->resolveType($form);
        $filter->filter($this->qb, $form, ['field' => 'foo', 'dataType' => 'association', 'associationType' => ClassMetadata::TO_ONE]);
        $this->assertSame(static::FILTER_TYPE, $filter::class);
        $this->assertSame($dql, $this->qb->getDQL());
        $this->assertSameDoctrineParams($params, $this->qb->getParameters()->toArray());
    }

    /**
     * @dataProvider getDataProviderToManyAssoc
     */
    public function testFilterToManyAssociationType($submittedData, $data, array $options, string $dql, array $params)
    {
        $entity1 = new SingleIntIdEntity(1, 'Foo');
        $entity2 = new SingleIntIdEntity(2, 'Bar');
        $this->persist([$entity1, $entity2]);

        $form = $this->factory->create(static::FILTER_TYPE, null, $options);
        $form->submit($submittedData);
        $this->assertEquals($data, $form->getData());
        $this->assertEmpty($form->getExtraData());
        $this->assertTrue($form->isSynchronized());

        $filter = $this->filterRegistry->resolveType($form);
        $filter->filter($this->qb, $form, ['field' => 'foo', 'dataType' => 'association', 'associationType' => ClassMetadata::TO_MANY]);
        $this->assertSame(static::FILTER_TYPE, $filter::class);
        $this->assertSame($dql, $this->qb->getDQL());
        $this->assertSameDoctrineParams($params, $this->qb->getParameters()->toArray());
    }

    public static function getDataProviderToOneAssoc(): iterable
    {
        $entity1 = new SingleIntIdEntity(1, 'Foo');
        $entity2 = new SingleIntIdEntity(2, 'Bar');

        yield [
            ['comparison' => ComparisonType::EQ, 'value' => 2],
            ['comparison' => '=', 'value' => $entity2],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => false,
                ],
            ],
            'SELECT o FROM Object o WHERE o.foo = (:foo_1)',
            [new Parameter('foo_1', $entity2, \PDO::PARAM_STR)],
        ];

        yield [
            ['comparison' => ComparisonType::NEQ, 'value' => 1],
            ['comparison' => '!=', 'value' => $entity1],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => false,
                ],
            ],
            'SELECT o FROM Object o WHERE o.foo != (:foo_1) OR o.foo IS NULL',
            [new Parameter('foo_1', $entity1, \PDO::PARAM_STR)],
        ];

        yield [
            ['comparison' => ComparisonType::EQ, 'value' => null],
            ['comparison' => 'IS NULL', 'value' => null],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => false,
                ],
            ],
            'SELECT o FROM Object o WHERE o.foo IS NULL',
            [],
        ];

        yield [
            ['comparison' => ComparisonType::EQ, 'value' => []],
            ['comparison' => 'IS NULL', 'value' => new ArrayCollection()],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => true,
                ],
            ],
            'SELECT o FROM Object o WHERE o.foo IS NULL',
            [],
        ];
    }

    public static function getDataProviderToManyAssoc(): iterable
    {
        $entity1 = new SingleIntIdEntity(1, 'Foo');
        $entity2 = new SingleIntIdEntity(2, 'Bar');

        yield [
            ['comparison' => ComparisonType::EQ, 'value' => []],
            ['comparison' => 'IS NULL', 'value' => new ArrayCollection()],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => true,
                ],
            ],
            'SELECT o FROM Object o LEFT JOIN o.foo foo_2 WHERE foo_2 IS NULL',
            [],
        ];

        yield [
            ['comparison' => ComparisonType::EQ, 'value' => [1, 2]],
            ['comparison' => 'IN', 'value' => new ArrayCollection([$entity1, $entity2])],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => true,
                ],
            ],
            'SELECT o FROM Object o LEFT JOIN o.foo foo_2 WHERE foo_2 IN (:foo_1)',
            [new Parameter('foo_1', new ArrayCollection([$entity1, $entity2]), \PDO::PARAM_STR)],
        ];

        yield [
            ['comparison' => ComparisonType::NEQ, 'value' => [1, 2]],
            ['comparison' => 'NOT IN', 'value' => new ArrayCollection([$entity1, $entity2])],
            [
                'value_type_options' => [
                    'em' => 'default',
                    'class' => SingleIntIdEntity::class,
                    'multiple' => true,
                ],
            ],
            'SELECT o FROM Object o LEFT JOIN o.foo foo_2 WHERE foo_2 NOT IN (:foo_1) OR foo_2 IS NULL',
            [new Parameter('foo_1', new ArrayCollection([$entity1, $entity2]), \PDO::PARAM_STR)],
        ];
    }

    protected function getExtensions()
    {
        return array_merge(parent::getExtensions(), [
            new DoctrineOrmExtension($this->emRegistry),
        ]);
    }

    protected function persist(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }

        $this->em->flush();
        // no clear, because entities managed by the choice field must
        // be managed!
    }

    protected function createRegistryMock($name, $em)
    {
        $registry = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        $registry->method('getManager')
            ->with($this->equalTo($name))
            ->willReturn($em);

        return $registry;
    }
}

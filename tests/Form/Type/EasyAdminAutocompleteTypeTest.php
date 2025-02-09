<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Form\Type;

use AppTestBundle\Entity\UnitTests\Category;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EasyAdminAutocompleteTypeTest extends TypeTestCase
{
    public const ENTITY_CLASS = 'AppTestBundle\Entity\UnitTests\Category';

    private $doctrine;
    private $entityManager;
    private $classMetadata;
    private $repository;
    private $configManager;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->classMetadata = $this->getMockBuilder('Doctrine\Persistence\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $this->classMetadata
            ->expects($this->any())
            ->method('getIdentifierFieldNames')
            ->willReturn(['id']);
        $this->classMetadata
            ->expects($this->any())
            ->method('getTypeOfField')
            ->willReturn('integer');

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->with(self::ENTITY_CLASS)
            ->willReturn($this->repository);
        $this->entityManager
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with(self::ENTITY_CLASS)
            ->willReturn($this->classMetadata);

        $this->doctrine = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->doctrine
            ->expects($this->any())
            ->method('getManagerForClass')
            ->with(self::ENTITY_CLASS)
            ->willReturn($this->entityManager);

        $this->configManager = $this->createConfigManagerMock();

        parent::setUp();
    }

    protected function getExtensions()
    {
        $types = [
            'entity' => new EntityType($this->doctrine),
            'easyadmin_autocomplete' => new EasyAdminAutocompleteType($this->configManager),
        ];

        return [
            new PreloadedExtension($types, []),
        ];
    }

    public function testSubmitValidSingleData()
    {
        $category = new Category();
        $category->id = 1;

        $this->entityManager
            ->expects($this->any())
            ->method('contains')
            ->with($category)
            ->willReturn(true);

        $this->repository
            ->expects($this->any())
            ->method('findBy')
            ->willReturn([$category]);

        $this->classMetadata
            ->expects($this->any())
            ->method('getIdentifierValues')
            ->with($category)
            ->willReturn(['id' => $category->id]);

        $form = $this->factory->create(EasyAdminAutocompleteType::class, null, [
            'class' => self::ENTITY_CLASS,
        ]);
        $formData = ['autocomplete' => '1'];
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($category, $form->getData());

        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        if (class_exists('Symfony\Component\Form\ChoiceList\View\ChoiceView', false)) {
            $choiceView = new \Symfony\Component\Form\ChoiceList\View\ChoiceView($category, 1, '1');
        } else {
            $choiceView = new \Symfony\Component\Form\Extension\Core\View\ChoiceView($category, 1, '1');
        }

        $this->assertEquals(['1' => $choiceView], $children['autocomplete']->vars['choices']);
    }

    public function testSubmitValidMultipleData()
    {
        $category1 = new Category();
        $category1->id = 1;

        $this->entityManager
            ->expects($this->any())
            ->method('contains')
            ->with($category1)
            ->willReturn(true);

        $this->repository
            ->expects($this->any())
            ->method('findBy')
            ->withAnyParameters()
            ->willReturn([$category1]);

        $this->classMetadata
            ->expects($this->any())
            ->method('getIdentifierValues')
            ->with($category1)
            ->willReturn(['id' => $category1->id]);

        $form = $this->factory->create(EasyAdminAutocompleteType::class, null, [
            'class' => self::ENTITY_CLASS,
            'multiple' => true,
        ]);
        $form->submit(['autocomplete' => ['1']]);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(new ArrayCollection([$category1]), $form->getData());
    }

    public function testSubmitEmptySingleData()
    {
        $form = $this->factory->create(EasyAdminAutocompleteType::class, null, [
            'class' => self::ENTITY_CLASS,
        ]);
        $form->submit(['autocomplete' => '']);

        $this->assertTrue($form->isSynchronized());
        $this->assertNull($form->getData());
    }

    public function testSubmitEmptyMultipleData()
    {
        $form = $this->factory->create(EasyAdminAutocompleteType::class, null, [
            'class' => self::ENTITY_CLASS,
            'multiple' => true,
        ]);
        $form->submit(null);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(new ArrayCollection(), $form->getData());
    }

    /**
     * The ConfigManager class is final, so it cannot be mocked easily.
     */
    private function createConfigManagerMock()
    {
        $processedConfig = ['entities' => [
            'Category' => [
                'class' => 'AppTestBundle\Entity\UnitTests\Category',
                'name' => 'Category',
            ],
        ]];

        $cache = new ArrayAdapter();
        // the name must be like the private const ConfigManager::CACHE_KEY
        $cacheItem = $cache->getItem('easyadmin.processed_config');
        $cacheItem->set($processedConfig);
        $cache->save($cacheItem);

        return new ConfigManager([], false, new PropertyAccessor(), $cache);
    }
}

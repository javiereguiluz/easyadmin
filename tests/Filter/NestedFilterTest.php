<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\Configurator\NestedConfigurator;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NestedFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NestedFilterTest extends KernelTestCase
{
    /** @var Registry */
    private $doctrine;

    /** @var NestedConfigurator */
    private $nestedConfigurator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->doctrine = $container->get('doctrine');
        $this->nestedConfigurator = $container->get(NestedConfigurator::class);
    }

    public function testWrap()
    {
        $wrappedFilter = TextFilter::new('blogPosts.categories');
        $nestedFilter = NestedFilter::wrap($wrappedFilter);

        self::assertEquals($wrappedFilter, $nestedFilter->getWrappedFilter());
        // Does not wrap filter if no dot found in path
        self::assertInstanceOf(TextFilter::class, NestedFilter::wrap(TextFilter::new('blogPosts')));
    }

    /**
     * @dataProvider getTestApplyDataProvider
     */
    public function testApply(string $class, FilterInterface $wrapperFilter, array $values, string $result, string $exceptionMessage = '')
    {
        $alias = 'o';
        $objectManager = $this->doctrine->getManagerForClass($class);
        $queryBuilder = $objectManager->getRepository($class)->createQueryBuilder($alias);

        $filter = NestedFilter::wrap($wrapperFilter);
        $filterDataDto = FilterDataDto::new(0, $filter->getAsDto(), $alias, $values);
        $entityDto = new EntityDto($class, $objectManager->getClassMetadata($class));
        $adminContext = $this->getMockBuilder(AdminContext::class)->disableOriginalConstructor()->getMock();

        try {
            $this->nestedConfigurator->configure($filter->getAsDto(), null, $entityDto, $adminContext);
            $filter->apply($queryBuilder, $filterDataDto, null, $entityDto);

            self::assertEquals($result, $queryBuilder->getQuery()->getDQL());
            self::assertEquals($values['value'], $queryBuilder->getParameters()[0]->getValue());
        } catch (\Exception $e) {
            // Expect exception
            if (\Exception::class === $result) {
                self::assertEquals($e->getMessage(), $exceptionMessage);

                return;
            }

            throw $e;
        }

        try {
            $queryBuilder->getQuery()->getResult();
            $executionSuccedeed = true;
        } catch (\Exception $e) {
            $executionSuccedeed = false;
        }

        self::assertTrue($executionSuccedeed);
    }

    public function getTestApplyDataProvider(): iterable
    {
        yield [
            Category::class,
            TextFilter::new('blogPosts.author.name'),
            ['value' => 'foo', 'value2' => null, 'comparison' => '='],
            'SELECT o FROM '.Category::class.' o LEFT JOIN o.blogPosts o_blogPosts LEFT JOIN o_blogPosts.author o_blogPosts_author WHERE o_blogPosts_author.name = :name_0',
        ];

        yield [
            BlogPost::class,
            TextFilter::new('author.name'),
            ['value' => 'foo', 'value2' => null, 'comparison' => '='],
            'SELECT o FROM '.BlogPost::class.' o LEFT JOIN o.author o_author WHERE o_author.name = :name_0',
        ];

        yield [
            User::class,
            EntityFilter::new('blogPosts.categories'),
            ['value' => [1, 2], 'value2' => null, 'comparison' => 'IN'],
            'SELECT o FROM '.User::class.' o LEFT JOIN o.blogPosts o_blogPosts LEFT JOIN o_blogPosts.categories ea_categories_0 WHERE ea_categories_0 IN (:categories_0)',
        ];

        yield [
            User::class,
            TextFilter::new('blogPosts.author.blogPosts.categories.name'),
            ['value' => 'foo', 'value2' => null, 'comparison' => '='],
            'SELECT o FROM '.User::class.' o LEFT JOIN o.blogPosts o_blogPosts LEFT JOIN o_blogPosts.author o_blogPosts_author LEFT JOIN o_blogPosts_author.blogPosts o_blogPosts_author_blogPosts LEFT JOIN o_blogPosts_author_blogPosts.categories o_blogPosts_author_blogPosts_categories WHERE o_blogPosts_author_blogPosts_categories.name = :name_0',
        ];

        yield [
            User::class,
            TextFilter::new('blogPosts.invalid.name'),
            ['value' => 'foo', 'value2' => null, 'comparison' => '='],
            \Exception::class,
            'The property path "blogPosts.invalid.name" for class "'.User::class.'" is invalid.',
        ];

        yield [
            User::class,
            TextFilter::new('blogPosts.author.name.test'),
            ['value' => 'foo', 'value2' => null, 'comparison' => '='],
            \Exception::class,
            'The property path "blogPosts.author.name.test" for class "'.User::class.'" is invalid.',
        ];
    }
}

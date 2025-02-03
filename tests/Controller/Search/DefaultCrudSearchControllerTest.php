<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller\Search;

use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\Search\DefaultCrudSearchController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\User;

class DefaultCrudSearchControllerTest extends AbstractCrudTestCase
{
    protected function getControllerFqcn(): string
    {
        return DefaultCrudSearchController::class;
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

    public function testDefaultEmptySearchForm()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $this->assertSelectorNotExists('form.form-action-search .content-search-reset', 'The empty search form should not display the button to reset contents');

        $form = $crawler->filter('form.form-action-search');
        $this->assertSelectorNotExists('input[type="hidden"][name="crudAction"]', 'This form field was used in EasyAdmin 4.x but shouldn\'t be present in EasyAdmin 5.x');
        $this->assertSelectorNotExists('input[type="hidden"][name="crudControllerFqcn"]', 'This form field was used in EasyAdmin 4.x but shouldn\'t be present in EasyAdmin 5.x');
        $this->assertSelectorNotExists('input[type="hidden"][name="page"]', 'This form field was used in EasyAdmin 4.x but shouldn\'t be present in EasyAdmin 5.x');

        $this->assertSame('http://localhost/admin/default-crud-search?page=1', $form->attr('action'));

        $formSearchInput = $form->filter('input[name="query"]');
        $this->assertSame('', $formSearchInput->attr('value'));
        $this->assertSame('Search', $formSearchInput->attr('placeholder'));
        $this->assertSame('false', $formSearchInput->attr('spellcheck'));
        $this->assertSame('off', $formSearchInput->attr('autocorrect'));
    }

    public function testSearchFormAfterMakingAQuery()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl('blog post'));

        $form = $crawler->filter('form.form-action-search');
        $this->assertSame('http://localhost/admin/default-crud-search?page=1&query=blog%20post', $form->attr('action'));

        $formSearchInput = $form->filter('input[name="query"]');
        $this->assertSame('blog post', $formSearchInput->attr('value'));
        $this->assertSame('Search', $formSearchInput->attr('placeholder'));
        $this->assertSame('false', $formSearchInput->attr('spellcheck'));
        $this->assertSame('off', $formSearchInput->attr('autocorrect'));

        $this->assertSelectorExists('form.form-action-search .content-search-reset', 'After making a query, the search form should display the button to reset contents');
        $this->assertSame($this->generateIndexUrl(), $crawler->filter('form.form-action-search .content-search-reset')->attr('href'));
    }

    public function testPaginationNotDisplayedWhenNotNeeded()
    {
        // Browse the index page and make some query
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $form = $crawler->filter('form.form-action-search')->form();
        $crawler = $this->client->submit($form, ['query' => 'blog-post-0']);

        // assert that the pagination is not displayed because there are not enough results
        $form = $crawler->filter('form.form-action-search');
        $this->assertSame('http://localhost/admin/default-crud-search?page=1&query=blog-post-0', $form->attr('action'));
        $this->assertSame('1', $crawler->filter('.list-pagination .list-pagination-counter strong')->text());
        $this->assertSelectorNotExists('.list-pagination nav.pager');
    }

    public function testPaginationAndSortingIsResetAfterAQuery()
    {
        // Browse the index page, click on 'next page' and click on a table column to sort the results
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $crawler = $this->client->click($crawler->selectLink('Next')->link());
        $crawler = $this->client->click($crawler->filter('th[data-column="title"] a')->link());

        // now, make some query
        $form = $crawler->filter('form.form-action-search')->form();
        $crawler = $this->client->submit($form, ['query' => 'blog post']);

        // assert that the pagination and sorting is reset
        $form = $crawler->filter('form.form-action-search');
        $this->assertSame('http://localhost/admin/default-crud-search?page=1&query=blog%20post&sort%5Btitle%5D=DESC', $form->attr('action'));
        $this->assertSame('1', $crawler->filter('.page-item.active .page-link')->text());

        $this->assertCount(0, $crawler->filter('th[data-column="title"] a.sorted'));
    }

    public function testSearchIsPersistedAfterPaginationAndSorting()
    {
        // Make some query
        $crawler = $this->client->request('GET', $this->generateIndexUrl('blog post'));

        // now, click on 'next page' and click on a table column to sort the results
        $crawler = $this->client->click($crawler->selectLink('Next')->link());
        $crawler = $this->client->click($crawler->filter('th[data-column="title"] a')->link());

        // assert that the search query is persisted
        $form = $crawler->filter('form.form-action-search');
        $formSearchInput = $form->filter('input[name="query"]');
        $this->assertSame('blog post', $formSearchInput->attr('value'));
        $this->assertSelectorExists('form.form-action-search .content-search-reset');
    }

    /**
     * @dataProvider provideSearchTests
     */
    public function testSearch(array $newBlogPostsToCreate, string $query, int $expectedResultCount)
    {
        foreach ($newBlogPostsToCreate as $blogPostData) {
            $blogPost = $this->createBlogPost($blogPostData['title'], $blogPostData['slug']);
            $this->entityManager->persist($blogPost);
        }
        $this->entityManager->flush();

        $this->client->request('GET', $this->generateIndexUrl($query));
        static::assertIndexFullEntityCount($expectedResultCount);
    }

    public static function provideSearchTests(): iterable
    {
        $totalNumberOfPosts = 20;

        yield 'search all blog posts' => [
            [],
            'blog post',
            $totalNumberOfPosts,
        ];

        yield 'search all blog posts (use quotes)' => [
            [],
            '"blog post"',
            $totalNumberOfPosts,
        ];

        yield 'search all terms' => [
            [],
            'blog post-17',
            1,
        ];

        yield 'search all terms (inversed terms)' => [
            [],
            'post-17 blog',
            1,
        ];

        yield 'search all terms with quotes' => [
            [],
            '"post 17"',
            1,
        ];

        yield 'search all terms with quotes (inversed terms)' => [
            [],
            '"17 post"',
            0,
        ];

        yield 'quoted terms with inside quotes' => [
            [
                ['title' => 'Foo "Bar Baz', 'slug' => 'foo-bar-baz'],
            ],
            '"foo "bar"',
            1,
        ];

        yield 'multiple quoted terms' => [
            [],
            '"Blog post" "post 17"',
            1,
        ];

        yield 'multiple quoted terms and unquoted terms' => [
            [],
            '"Blog post" "post 17" post ipsum',
            1,
        ];
    }

    private function createBlogPost(string $title, string $slug): BlogPost
    {
        $author = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user0@example.com']);

        return (new BlogPost())
            ->setTitle($title)
            ->setSlug($slug)
            ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setAuthor($author);
    }
}

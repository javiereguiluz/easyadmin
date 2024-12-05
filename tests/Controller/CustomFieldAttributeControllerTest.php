<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\CustomFieldAttributeCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\SecureDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\BlogPost;

class CustomFieldAttributeControllerTest extends AbstractCrudTestCase
{
    protected EntityRepository $blogPosts;

    protected function getControllerFqcn(): string
    {
        return CustomFieldAttributeCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return SecureDashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->client->setServerParameters(['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => '1234']);

        $this->blogPosts = $this->entityManager->getRepository(BlogPost::class);
    }

    public function testFieldAttributesOnIndexPage(): void
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        static::assertCount(20, $crawler->filter('td[multi-test-one="test1"]'));
        static::assertCount(20, $crawler->filter('td[multi-test-two="test2"]'));
        static::assertCount(20, $crawler->filter('td[data-some-attribute="some-value"]'));
        static::assertCount(20, $crawler->filter('td[x-attribute-two="another value"]'));
        static::assertCount(20, $crawler->filter('td[foo="3"]'));
        static::assertCount(20, $crawler->filter('td[bar=""]'));
        static::assertCount(20, $crawler->filter('td[foo-bar="1"]'));
    }

    public function testFieldAttributesOnDetailPage(): void
    {
        $blogPost = $this->blogPosts->findOneBy([]);
        $crawler = $this->client->request('GET', $this->generateDetailUrl($blogPost->getId()));

        $titleContainerElement = $crawler->filter('div.field-group:contains("Blog Post 0")');
        static::assertSame('test1', $titleContainerElement->attr('multi-test-one'));
        static::assertSame('test2', $titleContainerElement->attr('multi-test-two'));

        $publishedAtContainerElement = $crawler->filter('div.field-group:contains("Published At")');
        static::assertSame('some-value', $publishedAtContainerElement->attr('data-some-attribute'));
        static::assertSame('another value', $publishedAtContainerElement->attr('x-attribute-two'));
        static::assertSame('3', $publishedAtContainerElement->attr('foo'));
        static::assertSame('', $publishedAtContainerElement->attr('bar'));
        static::assertSame('1', $publishedAtContainerElement->attr('foo-bar'));
    }

    public function testFieldAttributesOnEditPage(): void
    {
        $blogPost = $this->blogPosts->findOneBy([]);
        $crawler = $this->client->request('GET', $this->generateEditFormUrl($blogPost->getId()));

        $titleElement = $crawler->filter('input[name="BlogPost[title]"]');
        static::assertSame('test1', $titleElement->attr('multi-test-one'));
        static::assertSame('test2', $titleElement->attr('multi-test-two'));

        $publishedAtElement = $crawler->filter('input[name="BlogPost[publishedAt]"]');
        static::assertSame('some-value', $publishedAtElement->attr('data-some-attribute'));
        static::assertSame('another value', $publishedAtElement->attr('x-attribute-two'));
        static::assertSame('3', $publishedAtElement->attr('foo'));
        static::assertNull($publishedAtElement->attr('bar'), 'Boolean attributes with FALSE value (e.g. bar="false") are not rendered in the HTML');
        static::assertSame('foo-bar', $publishedAtElement->attr('foo-bar'), 'Boolean attributes with TRUE value (e.g. foo-bar="true") are rendered as foo-bar="foo-bar"');
    }

    public function testFieldAttributesOnNewPage(): void
    {
        $blogPost = $this->blogPosts->findOneBy([]);
        $crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $titleElement = $crawler->filter('input[name="BlogPost[title]"]');
        static::assertSame('test1', $titleElement->attr('multi-test-one'));
        static::assertSame('test2', $titleElement->attr('multi-test-two'));

        $publishedAtElement = $crawler->filter('input[name="BlogPost[publishedAt]"]');
        static::assertSame('some-value', $publishedAtElement->attr('data-some-attribute'));
        static::assertSame('another value', $publishedAtElement->attr('x-attribute-two'));
        static::assertSame('3', $publishedAtElement->attr('foo'));
        static::assertNull($publishedAtElement->attr('bar'), 'Boolean attributes with FALSE value (e.g. bar="false") are not rendered in the HTML');
        static::assertSame('foo-bar', $publishedAtElement->attr('foo-bar'), 'Boolean attributes with TRUE value (e.g. foo-bar="true") are rendered as foo-bar="foo-bar"');
    }
}

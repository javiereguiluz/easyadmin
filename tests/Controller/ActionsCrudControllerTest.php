<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\ActionsCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\SecureDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Category;

class ActionsCrudControllerTest extends AbstractCrudTestCase
{
    protected EntityRepository $categories;

    protected function getControllerFqcn(): string
    {
        return ActionsCrudController::class;
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

        $this->categories = $this->entityManager->getRepository(Category::class);
    }

    public function testCssClasses()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        static::assertSame('dropdown-item action-action1', $crawler->filter('a.dropdown-item:contains("Action1")')->attr('class'));
        static::assertSame('dropdown-item foo', $crawler->filter('a.dropdown-item:contains("Action2")')->attr('class'));
        static::assertSame('dropdown-item action-action3 bar', $crawler->filter('a.dropdown-item:contains("Action3")')->attr('class'));
        static::assertSame('dropdown-item foo bar', $crawler->filter('a.dropdown-item:contains("Action4")')->attr('class'));

        static::assertSame('action-new btn btn-primary', trim($crawler->filter('.global-actions > a')->first()->attr('class')));
    }

    public function testDynamicLabels()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        static::assertSame('Action 5: Category 0', $crawler->filter('a.dropdown-item[data-action-name="action5"]')->text());
        static::assertSame('Action 6: Category 0', $crawler->filter('a.dropdown-item[data-action-name="action6"]')->text());
        static::assertSame('Action 7: Category 0', $crawler->filter('a.dropdown-item[data-action-name="action7"]')->text());
        static::assertSame('Reset', $crawler->filter('a.dropdown-item[data-action-name="action8"]')->text());
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Router;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller\BlogPostCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller\CategoryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller\DashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller\SecondDashboardController;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\Filesystem\Filesystem;

class AdminRouteGeneratorTest extends WebTestCase
{
    /**
     * @dataProvider provideFindRouteData
     */
    public function testFindRoute(?string $dashboardControllerFqcn, ?string $crudControllerFqcn, ?string $action, ?string $expectedRouteName)
    {
        $client = static::createClient();
        $cacheMock = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheMock->method('getItem')->willReturnCallback(function ($key) {
            $item = new CacheItem();
            $item->expiresAfter(3600);

            if (AdminRouteGenerator::CACHE_KEY_FQCN_TO_ROUTE !== $key) {
                return $item;
            }

            $item->set([
                DashboardController::class => [
                    '' => [
                        '' => 'admin',
                    ],
                    BlogPostCrudController::class => [
                        'index' => 'admin_post_index',
                        'new' => 'admin_post_new',
                        'edit' => 'admin_post_edit',
                        'detail' => 'admin_post_detail',
                    ],
                ],
                SecondDashboardController::class => [
                    '' => [
                        '' => 'second_admin',
                    ],
                ],
            ]);

            return $item;
        });

        $dashboardControllers = new RewindableGenerator(function () {
            yield DashboardController::class => new DashboardController();
            yield SecondDashboardController::class => new SecondDashboardController();
        }, 2);

        $adminRouteGenerator = new AdminRouteGenerator(
            $dashboardControllers,
            [],
            $cacheMock,
            new Filesystem(),
            $client->getKernel()->getBuildDir(),
        );

        $routeName = $adminRouteGenerator->findRouteName($dashboardControllerFqcn, $crudControllerFqcn, $action);
        $this->assertSame($expectedRouteName, $routeName);
    }

    public function provideFindRouteData(): iterable
    {
        yield [null, null, null, 'admin'];
        yield [DashboardController::class, null, null, 'admin'];
        yield [DashboardController::class, BlogPostCrudController::class, null, null];
        yield [DashboardController::class, BlogPostCrudController::class, 'index', 'admin_post_index'];
        yield [DashboardController::class, BlogPostCrudController::class, 'detail', 'admin_post_detail'];
        yield [DashboardController::class, CategoryCrudController::class, null, null];
        yield [DashboardController::class, CategoryCrudController::class, 'index', null];
        yield [DashboardController::class, CategoryCrudController::class, 'detail', null];
        yield [SecondDashboardController::class, null, null, 'second_admin'];
        yield [SecondDashboardController::class, BlogPostCrudController::class, null, null];
        yield [SecondDashboardController::class, BlogPostCrudController::class, 'index', null];
        yield [SecondDashboardController::class, BlogPostCrudController::class, 'detail', null];
    }
}

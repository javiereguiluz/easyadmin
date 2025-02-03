<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Router;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Cache;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Context\AdminContextInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class AdminUrlGeneratorTest extends WebTestCase
{
    use ExpectDeprecationTrait;

    protected static $container;

    public function testGenerateEmptyUrl()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $this->assertSame('http://localhost/admin', $adminUrlGenerator->generateUrl());
    }

    public function testGetRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $this->assertSame('bar', $adminUrlGenerator->get('foo'));
        $this->assertNull($adminUrlGenerator->get('this_query_param_does_not_exist'));
    }

    public function testSetRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->set('foo', 'not_bar');
        $this->assertSame('http://localhost/admin?foo=not_bar', $adminUrlGenerator->generateUrl());
    }

    public function testNullParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->set('param1', null);
        $adminUrlGenerator->set('param2', 'null');
        $this->assertSame('http://localhost/admin?foo=bar&param2=null', $adminUrlGenerator->generateUrl());
    }

    public function testSetAll()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->setAll(['foo1' => 'bar1', 'foo2' => 'bar2']);
        $this->assertSame('http://localhost/admin?foo=bar&foo1=bar1&foo2=bar2', $adminUrlGenerator->generateUrl());
    }

    public function testUnsetAll()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->set('foo1', 'bar1');
        $adminUrlGenerator->unsetAll();
        $this->assertSame('http://localhost/admin', $adminUrlGenerator->generateUrl());
    }

    public function testUnsetAllExcept()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->setAll(['foo1' => 'bar1', 'foo2' => 'bar2', 'foo3' => 'bar3', 'foo4' => 'bar4']);
        $adminUrlGenerator->unsetAllExcept('foo3', 'foo2');
        $this->assertSame('http://localhost/admin?foo2=bar2&foo3=bar3', $adminUrlGenerator->generateUrl());
    }

    public function testParametersAreSorted()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->setAll(['1_foo' => 'bar', 'a_foo' => 'bar', '2_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setAll(['2_foo' => 'bar', 'a_foo' => 'bar', '1_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setAll(['a_foo' => 'bar', '2_foo' => 'bar', '1_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());
    }

    public function testUrlParametersDontAffectOtherUrls()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(requestQueryParams: ['foo' => 'bar']);

        $adminUrlGenerator->set('page', '1');
        $adminUrlGenerator->set('sort', ['id' => 'ASC']);
        $this->assertSame('http://localhost/admin?foo=bar&page=1&sort%5Bid%5D=ASC', $adminUrlGenerator->generateUrl());

        $this->assertSame('http://localhost/admin?foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->set('page', '2');
        $this->assertSame('http://localhost/admin?foo=bar&page=2', $adminUrlGenerator->generateUrl());
        $this->assertNull($adminUrlGenerator->get('sort'));
    }

    public function testExplicitDashboardController()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setDashboard(AdminUrlGeneratorTestSecondDashboardController::class);
        $this->assertSame('http://localhost/second/admin', $adminUrlGenerator->generateUrl());
    }

    public function testUnknownExplicitDashboardController()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given "ThisDashboardControllerDoesNotExist" class is not a valid Dashboard controller. Make sure it extends "EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController" or implements "EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface".');

        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setDashboard('ThisDashboardControllerDoesNotExist');
        $adminUrlGenerator->generateUrl();
    }

    public function testDefaultCrudAction()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setController('FooController');
        $this->assertSame('http://localhost/admin', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setController('FooController');
        $adminUrlGenerator->setAction(Action::NEW);
        $this->assertSame('http://localhost/admin', $adminUrlGenerator->generateUrl());
    }

    public function testControllerParameterRemovesRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setController('App\Controller\Admin\SomeCrudController');
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_NAME));
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_PARAMS));

        $adminUrlGenerator->setRoute('some_route', ['key' => 'value']);
        $adminUrlGenerator->setController('App\Controller\Admin\SomeCrudController');
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_NAME));
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_PARAMS));
    }

    public function testActionParameterRemovesRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setAction(Action::INDEX);
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_NAME));
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_PARAMS));

        $adminUrlGenerator->setRoute('some_route', ['key' => 'value']);
        $adminUrlGenerator->setAction(Action::INDEX);
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_NAME));
        $this->assertNull($adminUrlGenerator->get(EA::ROUTE_PARAMS));
    }

    public function testRouteParametersRemoveOtherParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setRoute('some_route', ['key' => 'value']);
        $this->assertNull($adminUrlGenerator->get(EA::CRUD_CONTROLLER_FQCN));

        $adminUrlGenerator->setController(AdminUrlGeneratorTestBlogPostCrudController::class);
        $adminUrlGenerator->set('foo', 'bar');
        $adminUrlGenerator->setRoute('some_route', ['key' => 'value']);

        $this->assertNull($adminUrlGenerator->get(EA::CRUD_CONTROLLER_FQCN));
        $this->assertSame('bar', $adminUrlGenerator->get('foo'));
    }

    public function testGeneratedUrlsContainNoReferrerByDefault()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $this->assertStringNotContainsString('referrer', $adminUrlGenerator->generateUrl(), 'The referrer query string parameter was deprecated in 4.x version');
    }

    public function testRelativeUrls()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(false);

        $adminUrlGenerator->set('foo', 'bar');
        $adminUrlGenerator->setController(AdminUrlGeneratorTestBlogPostCrudController::class);
        $this->assertSame('/admin/post?foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->set('foo', 'bar');
        $adminUrlGenerator->setController(AdminUrlGeneratorTestBlogPostCrudController::class);
        $this->assertSame('http://localhost/admin/post?foo=bar', $adminUrlGenerator->generateUrl());
    }

    private function getAdminUrlGenerator(bool $useAbsoluteUrls = true, array $requestQueryParams = [], array $requestAttributes = [EA::DASHBOARD_CONTROLLER_FQCN => DashboardController::class]): AdminUrlGeneratorInterface
    {
        self::bootKernel();

        $request = new Request(query: $requestQueryParams, attributes: $requestAttributes);

        $adminContext = $this->getMockBuilder(AdminContextInterface::class)->disableOriginalConstructor()->getMock();
        $adminContext->method('getDashboardRouteName')->willReturn('admin');
        $adminContext->method('getAbsoluteUrls')->willReturn($useAbsoluteUrls);
        $adminContext->method('getRequest')->willReturn($request);

        $adminContextProviderMock = $this->getMockBuilder(AdminContextProviderInterface::class)->disableOriginalConstructor()->getMock();
        $adminContextProviderMock->method('getContext')->willReturn($adminContext);

        $routerMock = $this->getMockBuilder(RouterInterface::class)->getMock();
        $routerMock->method('generate')->willReturnCallback(function ($name, $parameters) use ($useAbsoluteUrls) {
            $nameParts = explode('_', $name);
            if ('index' === end($nameParts)) {
                array_pop($nameParts);
            }
            if (\array_key_exists(EA::ENTITY_ID, $parameters)) {
                array_splice($nameParts, -1, 0, $parameters[EA::ENTITY_ID]);
            }
            unset($parameters[EA::ENTITY_ID]);

            $queryString = '';
            if ([] !== $parameters) {
                $queryString = '?'.http_build_query($parameters);
            }

            return ($useAbsoluteUrls ? 'http://localhost/' : '/').implode('/', $nameParts).$queryString;
        });

        $cacheMock = $this->getMockBuilder(CacheItemPoolInterface::class)->getMock();
        $cacheMock->method('getItem')->willReturnCallback(function ($key) {
            $item = new CacheItem();
            $item->expiresAfter(3600);

            if (Cache::ROUTE_ATTRIBUTES_TO_NAME === $key) {
                $item->set([
                    AdminUrlGeneratorTestDashboardController::class => [
                        '' => [
                            '' => 'admin',
                        ],
                        AdminUrlGeneratorTestBlogPostCrudController::class => [
                            'index' => 'admin_post_index',
                            'new' => 'admin_post_new',
                            'edit' => 'admin_post_edit',
                            'detail' => 'admin_post_detail',
                        ],
                    ],
                    AdminUrlGeneratorTestSecondDashboardController::class => [
                        '' => [
                            '' => 'second_admin',
                        ],
                    ],
                ]);
            } elseif (Cache::DASHBOARD_FQCN_TO_ROUTE === $key) {
                $item->set([
                    AdminUrlGeneratorTestDashboardController::class => 'admin',
                    AdminUrlGeneratorTestSecondDashboardController::class => 'second_admin',
                ]);
            }

            return $item;
        });

        $dashboardControllers = new RewindableGenerator(function () {
            yield AdminUrlGeneratorTestDashboardController::class => new AdminUrlGeneratorTestDashboardController();
            yield AdminUrlGeneratorTestSecondDashboardController::class => new AdminUrlGeneratorTestSecondDashboardController();
        }, 2);

        $adminRouteGenerator = new AdminRouteGenerator($dashboardControllers, [], $cacheMock, 'en');

        return new AdminUrlGenerator($adminContextProviderMock, $routerMock, $adminRouteGenerator);
    }
}

class AdminUrlGeneratorTestDashboardController
{
}
class AdminUrlGeneratorTestSecondDashboardController
{
}
class AdminUrlGeneratorTestBlogPostCrudController
{
}

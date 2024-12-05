<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Router;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Registry\DashboardControllerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

class AdminUrlGeneratorTest extends WebTestCase
{
    use ExpectDeprecationTrait;

    protected static $container;

    public function testGenerateEmptyUrl()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        // the foo=bar query params come from the current request (defined in the mock of the setUp() method)
        $this->assertSame('http://localhost/admin?foo=bar', $adminUrlGenerator->generateUrl());
    }

    public function testGetRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $this->assertSame('bar', $adminUrlGenerator->get('foo'));
        $this->assertNull($adminUrlGenerator->get('this_query_param_does_not_exist'));
    }

    public function testSetRouteParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->set('foo', 'not_bar');
        $this->assertSame('http://localhost/admin?foo=not_bar', $adminUrlGenerator->generateUrl());
    }

    public function testNullParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->set('param1', null);
        $adminUrlGenerator->set('param2', 'null');
        $this->assertSame('http://localhost/admin?foo=bar&param2=null', $adminUrlGenerator->generateUrl());
    }

    public function testSetAll()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setAll(['foo1' => 'bar1', 'foo2' => 'bar2']);
        $this->assertSame('http://localhost/admin?foo=bar&foo1=bar1&foo2=bar2', $adminUrlGenerator->generateUrl());
    }

    public function testUnsetAll()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->set('foo1', 'bar1');
        $adminUrlGenerator->unsetAll();
        $this->assertSame('/admin', $adminUrlGenerator->generateUrl());
    }

    public function testUnsetAllExcept()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setAll(['foo1' => 'bar1', 'foo2' => 'bar2', 'foo3' => 'bar3', 'foo4' => 'bar4']);
        $adminUrlGenerator->unsetAllExcept('foo3', 'foo2');
        $this->assertSame('http://localhost/admin?foo2=bar2&foo3=bar3', $adminUrlGenerator->generateUrl());
    }

    public function testParametersAreSorted()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setAll(['1_foo' => 'bar', 'a_foo' => 'bar', '2_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setAll(['2_foo' => 'bar', 'a_foo' => 'bar', '1_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setAll(['a_foo' => 'bar', '2_foo' => 'bar', '1_foo' => 'bar']);
        $this->assertSame('http://localhost/admin?1_foo=bar&2_foo=bar&a_foo=bar&foo=bar', $adminUrlGenerator->generateUrl());
    }

    public function testUrlParametersDontAffectOtherUrls()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

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

        $adminUrlGenerator->setDashboard('App\Controller\Admin\SecureDashboardController');
        $this->assertSame('http://localhost/secure_admin?foo=bar', $adminUrlGenerator->generateUrl());
    }

    public function testUnknownExplicitDashboardController()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The given "ThisDashboardControllerDoesNotExist" class is not a valid Dashboard controller. Make sure it extends from "EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController" or implements "EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\DashboardControllerInterface".');

        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setDashboard('ThisDashboardControllerDoesNotExist');
        $adminUrlGenerator->generateUrl();
    }

    public function testDefaultCrudAction()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setController('FooController');
        $this->assertSame('http://localhost/admin?crudAction=index&crudControllerFqcn=FooController&foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setController('FooController');
        $adminUrlGenerator->setAction(Action::NEW);
        $this->assertSame('http://localhost/admin?crudAction=new&crudControllerFqcn=FooController&foo=bar', $adminUrlGenerator->generateUrl());
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

        $adminUrlGenerator->setController('App\Controller\Admin\SomeCrudController');
        $adminUrlGenerator->set('foo', 'bar');
        $adminUrlGenerator->setRoute('some_route', ['key' => 'value']);

        $this->assertNull($adminUrlGenerator->get(EA::CRUD_CONTROLLER_FQCN));
        $this->assertNull($adminUrlGenerator->get('foo'));
    }

    /**
     * @legacy
     */
    public function testLegacyParameters()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();
        $adminUrlGenerator->set(EA::MENU_INDEX, 3);

        $this->assertSame(3, $adminUrlGenerator->get(EA::MENU_INDEX));
    }

    /**
     * @group legacy
     */
    public function testDeprecatedParameterMessage()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();
        $this->expectDeprecation('Since easycorp/easyadmin-bundle 4.5.0: Using the "menuIndex" query parameter is deprecated. Menu items are now highlighted automatically based on the Request data, so you don\'t have to deal with menu items manually anymore.');
        $adminUrlGenerator->set('menuIndex', 1);
    }

    /**
     * @group legacy
     */
    public function testIncludeReferrer()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->includeReferrer();
        $this->assertSame('http://localhost/admin?foo=bar&referrer=/?foo%3Dbar', $adminUrlGenerator->generateUrl());
    }

    /**
     * @group legacy
     */
    public function testRemoveReferrer()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->removeReferrer();
        $this->assertSame('http://localhost/admin?foo=bar', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator->setReferrer('https://example.com/foo');
        $adminUrlGenerator->removeReferrer();
        $this->assertSame('http://localhost/admin?foo=bar', $adminUrlGenerator->generateUrl());
    }

    public function testNoReferrerByDefault()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $this->assertStringNotContainsString('referrer', $adminUrlGenerator->generateUrl());
    }

    /**
     * @group legacy
     */
    public function testCustomReferrer()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();

        $adminUrlGenerator->setReferrer('any_custom_value');
        $this->assertSame('http://localhost/admin?foo=bar&referrer=any_custom_value', $adminUrlGenerator->generateUrl());
    }

    /**
     * @group legacy
     */
    public function testPersistentCustomReferrer()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator();
        $adminUrlGenerator->setReferrer('any_custom_value');
        $this->assertSame('http://localhost/admin?foo=bar&referrer=any_custom_value', $adminUrlGenerator->generateUrl());

        // test that the custom referrer value does not persist after generating the URL
        $adminUrlGenerator->includeReferrer();
        $this->assertSame('http://localhost/admin?foo=bar&referrer=/?foo%3Dbar', $adminUrlGenerator->generateUrl());
    }

    public function testRelativeUrls()
    {
        $adminUrlGenerator = $this->getAdminUrlGenerator(false, true);

        $adminUrlGenerator->set('foo1', 'bar1');
        $adminUrlGenerator->setController('App\Controller\Admin\SomeCrudController');
        $this->assertSame('http://localhost/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CSomeCrudController&foo=bar&foo1=bar1', $adminUrlGenerator->generateUrl());

        $adminUrlGenerator = $this->getAdminUrlGenerator(true, true);

        $adminUrlGenerator->set('foo1', 'bar1');
        $adminUrlGenerator->setController('App\Controller\Admin\SomeCrudController');
        $this->assertSame('http://localhost/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CSomeCrudController&foo=bar&foo1=bar1', $adminUrlGenerator->generateUrl());
    }

    private function getAdminUrlGenerator(bool $signedUrls = false, bool $absoluteUrls = true): AdminUrlGeneratorInterface
    {
        self::bootKernel();

        $adminContext = $this->getMockBuilder(AdminContext::class)->disableOriginalConstructor()->getMock();
        $adminContext->method('getDashboardRouteName')->willReturn('admin');
        $adminContext->method('getSignedUrls')->willReturn($signedUrls);
        $adminContext->method('getAbsoluteUrls')->willReturn($absoluteUrls);
        $adminContext->method('getRequest')->willReturn(new Request(['foo' => 'bar']));

        $request = new Request();
        $request->query->set('foo', 'bar');
        $request->attributes->set(EA::CONTEXT_REQUEST_ATTRIBUTE, $adminContext);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $adminContextProvider = new AdminContextProvider($requestStack);

        $dashboardControllerRegistry = $this->getMockBuilder(DashboardControllerRegistry::class)->disableOriginalConstructor()->getMock();
        $dashboardControllerRegistry->method('getRouteByControllerFqcn')->willReturnMap([
            ['App\Controller\Admin\SecureDashboardController', 'secure_admin'],
        ]);
        $dashboardControllerRegistry->method('getNumberOfDashboards')->willReturn(2);
        $dashboardControllerRegistry->method('getFirstDashboardRoute')->willReturn('admin');

        $container = Kernel::MAJOR_VERSION >= 6 ? static::getContainer() : self::$container;
        $router = $container->get('router');

        $adminRouteGenerator = $this->getMockBuilder(AdminRouteGenerator::class)->disableOriginalConstructor()->getMock();

        return new AdminUrlGenerator($adminContextProvider, $router, $dashboardControllerRegistry, $adminRouteGenerator);
    }
}

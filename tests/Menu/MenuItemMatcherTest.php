<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use EasyCorp\Bundle\EasyAdminBundle\Menu\MenuItemMatcher;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminRouteGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class MenuItemMatcherTest extends KernelTestCase
{
    public function testIsSelectedWhenContextIsNull()
    {
        $request = $this->getRequestMock();

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItemDto = new MenuItemDto();

        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);

        $this->assertFalse($menuItemDto->isSelected());
    }

    public function testIsSelectedWhenMenuItemIsSection()
    {
        $request = $this->getRequestMock();

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItemDto = new MenuItemDto();
        $menuItemDto->setType(MenuItemDto::TYPE_SECTION);

        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);

        $this->assertFalse($menuItemDto->isSelected());
    }

    public function testIsSelectedWithCrudControllers()
    {
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\SomeController',
        );

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItemDto = $this->getMenuItemDto();
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);

        $this->assertFalse($menuItemDto->isSelected());

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'NOT_App\Controller\Admin\SomeController');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected(), 'The CRUD controller does not match');

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'App\Controller\Admin\SomeController');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected(), 'The CRUD controller matches');

        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\SomeController',
            getPrimaryKeyValue: '57',
            getCurrentAction: 'edit',
        );

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'App\Controller\Admin\SomeController', action: 'edit', entityId: '57');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected(), 'The CRUD controller and the entity ID match');

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'App\Controller\Admin\SomeController', action: 'edit', entityId: 'NOT_57');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected(), 'The entity ID of the menu item does not match');

        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\SomeController',
            getPrimaryKeyValue: '57',
            getCurrentAction: 'detail',
        );

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'App\Controller\Admin\SomeController', action: Crud::PAGE_DETAIL, entityId: '57');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected(), 'The CRUD controller, entity ID and action match');

        $menuItemDto = $this->getMenuItemDto(crudControllerFqcn: 'App\Controller\Admin\SomeController', action: 'NOT_'.Crud::PAGE_DETAIL, entityId: '57');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected(), 'The CRUD controller and entity ID match but the action does not match');
    }

    public function testIsSelectedWithRoutes()
    {
        $request = $this->getRequestMock(
            routeName: 'some_route',
        );

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route');

        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);

        $this->assertTrue($menuItemDto->isSelected(), 'The route name matches');

        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route', routeParameters: ['foo' => 'bar']);
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected());

        $request = $this->getRequestMock(
            routeName: 'some_route',
            routeParameters: ['foo1' => 'bar1', 'foo2' => 'bar2'],
        );

        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected());

        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route', routeParameters: ['foo1' => 'bar1']);
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected());

        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route', routeParameters: ['foo1' => 'bar1', 'foo2' => 'bar2']);
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected());

        $menuItemDto = $this->getMenuItemDto(routeName: 'some_route', routeParameters: ['foo2' => 'bar2', 'foo1' => 'bar1']);
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected(), 'A menu item with the same query parameters but in different order matches too.');
    }

    public function testIsSelectedWithUrls()
    {
        $request = $this->getRequestMock(
            getUri: 'https://example.com/foo?bar=baz',
        );

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItemDto = new MenuItemDto();

        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);

        $this->assertFalse($menuItemDto->isSelected(), 'The URL does not match');

        $menuItemDto = new MenuItemDto();
        $menuItemDto->setLinkUrl('https://example.com');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected(), 'The URL does not match');

        $menuItemDto = new MenuItemDto();
        $menuItemDto->setLinkUrl('https://example.com/foo');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertFalse($menuItemDto->isSelected(), 'The URL does not match');

        $menuItemDto = new MenuItemDto();
        $menuItemDto->setLinkUrl('https://example.com/foo?bar=baz');
        $menuItemMatcher->markSelectedMenuItem([$menuItemDto], $request);
        $this->assertTrue($menuItemDto->isSelected(), 'The URL matches');
    }

    public function testMenuWithDashboardItem()
    {
        $dashboardMenuItem = new MenuItemDto();
        $dashboardMenuItem->setLabel('item1');
        $dashboardMenuItem->setType(MenuItemDto::TYPE_DASHBOARD);

        $menuItems = [
            $dashboardMenuItem,
            $this->getMenuItemDto(label: 'item2', routeName: 'item2'),
        ];

        $request = $this->getRequestMock(
            routeName: 'item2',
        );

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);

        $this->assertSame('item2', $this->getSelectedMenuItemLabel($menuItems), 'Perfect match: Dashboard item');
    }

    public function testComplexMenu()
    {
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller1',
        );

        self::bootKernel();
        $adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
        $adminRouteGenerator = self::getContainer()->get(AdminRouteGenerator::class);
        $menuItemMatcher = new MenuItemMatcher($adminUrlGenerator, $adminRouteGenerator);

        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);

        $this->assertSame('item1', $this->getSelectedMenuItemLabel($menuItems), 'Perfect match: CRUD controller and action');
        $this->assertNull($this->getExpandedMenuItemLabel($menuItems), 'No menu item is marked as expanded');

        unset($menuItems);
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller1',
            getCurrentAction: 'edit',
            // the primary key value is missing on purpose in this example
        );
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);
        $this->assertSame('item2', $this->getSelectedMenuItemLabel($menuItems), 'Perfect match: CRUD controller and action');
        $this->assertNull($this->getExpandedMenuItemLabel($menuItems), 'No menu item is marked as expanded');

        unset($menuItems);
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller1',
            getCurrentAction: 'new',
        );
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);
        $this->assertSame('item5', $this->getSelectedMenuItemLabel($menuItems), 'Perfect match: CRUD controller and action');
        $this->assertSame('item4', $this->getExpandedMenuItemLabel($menuItems), 'A submenu item is matched, so its parent item must be marked as expanded');

        unset($menuItems);
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller2',
            getCurrentAction: 'new',
        );
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);
        $this->assertSame('item3', $this->getSelectedMenuItemLabel($menuItems), 'Approximate match: controller matches, action doesn\'t match; the item with the INDEX action is selected by default');
        $this->assertNull($this->getExpandedMenuItemLabel($menuItems), 'No menu item is marked as expanded');

        unset($menuItems);
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller2',
            getCurrentAction: 'edit',
            getPrimaryKeyValue: 'NOT_57',
        );
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);
        $this->assertNull($this->getSelectedMenuItemLabel($menuItems), 'No match: controller and action match, but query parameters don\'t');
        $this->assertNull($this->getExpandedMenuItemLabel($menuItems), 'No menu item is marked as expanded');

        unset($menuItems);
        $menuItems = $this->getComplexMenuItems();
        $request = $this->getRequestMock(
            getControllerFqcn: 'App\Controller\Admin\Controller3',
            getCurrentAction: 'new',
        );
        $menuItems = $menuItemMatcher->markSelectedMenuItem($menuItems, $request);
        $this->assertSame('item7', $this->getSelectedMenuItemLabel($menuItems), 'Approximate match: only the controller matches; the item with the INDEX action is selected');
        $this->assertSame('item4', $this->getExpandedMenuItemLabel($menuItems), 'A submenu item is matched, so its parent item must be marked as expanded');
    }

    private function getComplexMenuItems(): array
    {
        return [
            $this->getMenuItemDto(label: 'item1', crudControllerFqcn: 'App\Controller\Admin\Controller1'),
            $this->getMenuItemDto(label: 'item2', crudControllerFqcn: 'App\Controller\Admin\Controller1', action: 'edit'),
            $this->getMenuItemDto(label: 'item3', crudControllerFqcn: 'App\Controller\Admin\Controller2'),
            $this->getMenuItemDto(label: 'item4', subItems: [
                $this->getMenuItemDto(label: 'item5', crudControllerFqcn: 'App\Controller\Admin\Controller1', action: 'new'),
                $this->getMenuItemDto(label: 'item6', crudControllerFqcn: 'App\Controller\Admin\Controller2', action: 'edit', entityId: '57'),
                $this->getMenuItemDto(label: 'item7', crudControllerFqcn: 'App\Controller\Admin\Controller3'),
                // 'item8' is a duplicate of 'item1'; this is a mistake but done on purpose to test that the first match is selected
                $this->getMenuItemDto(label: 'item8', crudControllerFqcn: 'App\Controller\Admin\Controller1'),
            ]),
            $this->getMenuItemDto(label: 'item9', crudControllerFqcn: 'App\Controller\Admin\Controller4'),
        ];
    }

    private function getSelectedMenuItemLabel(array $menuItems): ?string
    {
        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isSelected()) {
                return $menuItemDto->getLabel();
            }

            if (null !== $subItems = $menuItemDto->getSubItems()) {
                if (null !== $label = $this->getSelectedMenuItemLabel($subItems)) {
                    return $label;
                }
            }
        }

        return null;
    }

    private function getExpandedMenuItemLabel(array $menuItems): ?string
    {
        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isExpanded()) {
                return $menuItemDto->getLabel();
            }
        }

        return null;
    }

    /**
     * For tests we need to simulate that the MenuItemDto has been fully built as
     * done in MenuFactory. To simplify tests, we just append the needed query parameters
     * to build the final menu item URL.
     */
    private function getMenuItemDto(?string $crudControllerFqcn = null, ?string $action = null, ?string $entityId = null, ?string $routeName = null, ?array $routeParameters = null, ?array $subItems = null, ?string $label = null): MenuItemDto
    {
        $menuItemDto = new MenuItemDto();
        $menuItemRouteParameters = [];

        if (null !== $label) {
            $menuItemDto->setLabel($label);
        }

        if (null !== $crudControllerFqcn) {
            $menuItemRouteParameters[EA::CRUD_CONTROLLER_FQCN] = $crudControllerFqcn;
        }

        if (null !== $action) {
            $menuItemRouteParameters[EA::CRUD_ACTION] = $action;
        } elseif (null === $action && null === $routeName) {
            $menuItemRouteParameters[EA::CRUD_ACTION] = Crud::PAGE_INDEX;
        }

        if (null !== $entityId) {
            $menuItemRouteParameters[EA::ENTITY_ID] = $entityId;
        }

        if (null !== $routeName) {
            $menuItemRouteParameters[EA::ROUTE_NAME] = $routeName;
        }

        if (null !== $routeParameters) {
            $menuItemRouteParameters[EA::ROUTE_PARAMS] = $routeParameters;
        }

        if (null !== $subItems) {
            $menuItemDto->setSubItems($subItems);
        }

        $menuItemDto->setRouteParameters($menuItemRouteParameters);
        $menuItemDto->setLinkUrl('/?'.http_build_query($menuItemDto->getRouteParameters()));

        return $menuItemDto;
    }

    private function getRequestMock(?string $getControllerFqcn = null, ?string $getPrimaryKeyValue = null, ?string $getCurrentAction = null, ?string $routeName = null, ?array $routeParameters = null, ?string $getUri = null): Request
    {
        $queryParameters = [];

        if (null !== $getControllerFqcn) {
            $queryParameters[EA::CRUD_CONTROLLER_FQCN] = $getControllerFqcn;
        }
        if (null !== $getCurrentAction) {
            $queryParameters[EA::CRUD_ACTION] = $getCurrentAction;
        }

        if (null !== $getPrimaryKeyValue) {
            $queryParameters[EA::ENTITY_ID] = $getPrimaryKeyValue;
        }

        if (null !== $routeName) {
            $queryParameters[EA::ROUTE_NAME] = $routeName;
        }
        if (null !== $routeParameters) {
            $queryParameters[EA::ROUTE_PARAMS] = $routeParameters;
        }

        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->query = new InputBag($queryParameters);
        $request->attributes = new InputBag($queryParameters);

        if (null !== $getUri) {
            $request->method('getUri')->willReturn($getUri);
        } else {
            $request->method('getUri')->willReturn('/?'.http_build_query($queryParameters));
        }

        return $request;
    }
}

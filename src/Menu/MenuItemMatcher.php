<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemMatcherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Router\AdminRouteGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class MenuItemMatcher implements MenuItemMatcherInterface
{
    public function __construct(
        private AdminUrlGeneratorInterface $adminUrlGenerator,
        private AdminRouteGeneratorInterface $adminRouteGenerator,
    ) {
    }

    /**
     * Given the full list of menu items, this method finds which item should be
     * marked as 'selected' based on the current page being visited by the user.
     * If the selected item is a submenu item, it also marks the parent menu item
     * as 'expanded'.
     *
     * It returns the full list of menu items, including the updated item(s) marked
     * as selected/expanded.
     *
     * @param MenuItemDto[] $menuItems
     *
     * @return MenuItemDto[]
     */
    public function markSelectedMenuItem(array $menuItems, Request $request): array
    {
        if ($this->adminRouteGenerator->usesPrettyUrls()) {
            $menuItems = $this->doMarkSelectedPrettyUrlsMenuItem($menuItems, $request);
        } else {
            $menuItems = $this->doMarkSelectedLegacyMenuItem($menuItems, $request);
        }

        $menuItems = $this->doMarkExpandedMenuItem($menuItems);

        return $menuItems;
    }

    /**
     * @deprecated because you can't decide which menu item to select by only looking at the menu item itself. You need to check all menu items at the same time to solve edge-cases like multiple menu items linking to the same action of the same entity
     * @see markSelectedMenuItem()
     */
    public function isSelected(MenuItemDto $menuItemDto): bool
    {
        @trigger_deprecation('easycorp/easyadmin-bundle', '4.11', 'The "%s()" method is deprecated. Use the "%s()" method instead.', __METHOD__, 'markSelectedMenuItem()');

        return false;
    }

    /**
     * @deprecated because you can't decide which menu item to render expanded by only looking at the menu item itself. You need to check all menu items at the same time.
     * @see markExpandedMenuItem()
     */
    public function isExpanded(MenuItemDto $menuItemDto): bool
    {
        @trigger_deprecation('easycorp/easyadmin-bundle', '4.11', 'The "%s()" method is deprecated. Use the "%s()" method instead.', __METHOD__, 'markExpandedMenuItem()');

        return false;
    }

    /**
     * @param MenuItemDto[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function doMarkSelectedLegacyMenuItem(array $menuItems, Request $request): array
    {
        // the menu-item matching is a 2-phase process:
        // 1) scan all menu items to list which controllers and actions are linked from the menu;
        //    this is needed because e.g. sometimes we match a menu item that doesn't have the exact
        //    action but links to the INDEX action of the same controller of the current request
        // 2) decide which is the most appropriate menu item (if any) to mark as selected based on the current request
        $controllersAndActionsLinkedInTheMenu = $this->getControllersAndActionsLinkedInTheMenu($menuItems);
        $currentPageQueryParameters = $request->query->all();
        $currentRequestUri = $request->getUri();

        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedLegacyMenuItem($subItems, $request));
            }

            $menuItemQueryString = null === $menuItemDto->getLinkUrl() ? null : parse_url($menuItemDto->getLinkUrl(), \PHP_URL_QUERY);

            $menuItemQueryParameters = [];
            if (\is_string($menuItemQueryString)) {
                parse_str($menuItemQueryString, $menuItemQueryParameters);
            }

            if ([] === $menuItemQueryParameters || [] === $currentPageQueryParameters) {
                if ($menuItemDto->getLinkUrl() === $currentRequestUri) {
                    $menuItemDto->setSelected(true);
                }

                continue;
            }

            // if the menu only contains links to the INDEX action of the CRUD controller,
            // match that menu item for all actions (EDIT, NEW, etc.) of the same controller;
            // this is not strictly correct, but backend users expect this behavior because it
            // makes the sidebar menu more predictable and easier to use
            $menuItemController = $menuItemQueryParameters[EA::CRUD_CONTROLLER_FQCN] ?? null;
            $currentPageController = $currentPageQueryParameters[EA::CRUD_CONTROLLER_FQCN] ?? null;
            $actionsLinkedInTheMenuForThisEntity = $controllersAndActionsLinkedInTheMenu[$currentPageController] ?? [];
            $menuOnlyLinksToIndexActionOfThisEntity = $actionsLinkedInTheMenuForThisEntity === [Crud::PAGE_INDEX];
            if ($menuItemController === $currentPageController && $menuOnlyLinksToIndexActionOfThisEntity) {
                $menuItemDto->setSelected(true);

                break;
            }

            // if the menu contains links to more than one action of the CRUD controller
            // (e.g. INDEX and NEW), and the action of the current page is not included
            // in that list, match the menu item with the INDEX action; this is again not
            // strictly correct, but it's the expected behavior by backend users
            $menuItemAction = $menuItemQueryParameters[EA::CRUD_ACTION] ?? null;
            $currentPageAction = $currentPageQueryParameters[EA::CRUD_ACTION] ?? null;
            $isCurrentPageActionLinkedInTheMenu = \in_array($currentPageAction, $actionsLinkedInTheMenuForThisEntity, true);
            if ($menuItemController === $currentPageController && Crud::PAGE_INDEX === $menuItemAction && !$isCurrentPageActionLinkedInTheMenu) {
                $menuItemDto->setSelected(true);

                break;
            }

            // otherwise, match the query parameters of each menu item with the query
            // parameters of the current request; before making the match, we remove
            // some irrelevant query parameters such as filters, sorting, pagination, etc.
            $menuItemQueryParametersToCompare = $this->filterIrrelevantQueryParameters($menuItemQueryParameters);
            $currentPageQueryParametersToCompare = $this->filterIrrelevantQueryParameters($currentPageQueryParameters);

            // needed so you can pass route parameters in any order
            $this->recursiveKsort($menuItemQueryParametersToCompare);
            $this->recursiveKsort($currentPageQueryParametersToCompare);

            if ($menuItemQueryParametersToCompare === $currentPageQueryParametersToCompare) {
                $menuItemDto->setSelected(true);

                break;
            }
        }

        return $menuItems;
    }

    /**
     * @param MenuItemDto[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function doMarkSelectedPrettyUrlsMenuItem(array $menuItems, Request $request): array
    {
        // the menu-item matching is a 2-phase process:
        // 1) traverse all menu items and try to find an exact match with the current URL
        // 2) if no exact match is found, traverse all menu items again and try to find a partial match
        $currentUrlWithoutHost = $request->getPathInfo();
        $currentUrlQueryParams = $request->query->all();
        unset($currentUrlQueryParams['sort'], $currentUrlQueryParams['page'], $currentUrlQueryParams['query']);
        // sort them because menu items always have their query parameters sorted
        ksort($currentUrlQueryParams);

        $normalizedCurrentUrl = $currentUrlWithoutHost;
        if ([] !== $currentUrlQueryParams) {
            $normalizedCurrentUrl .= '?'.http_build_query($currentUrlQueryParams);
        }

        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedPrettyUrlsMenuItem($subItems, $request));
            }

            if ($menuItemDto->getLinkUrl() === $normalizedCurrentUrl) {
                $menuItemDto->setSelected(true);

                return $menuItems;
            }
        }

        // If the current URL is a CRUD URL and the action is not 'index', attempt
        // to match the same URL with the 'index' action. This ensures e.g. that the
        // /admin/post menu item is highlighted when visiting related URLs such as
        // /admin/post/new, /admin/post/37/edit, etc.
        if (null === $crudControllerFqcn = $request->attributes->get(EA::CRUD_CONTROLLER_FQCN)) {
            return $menuItems;
        }

        $currentUrlWithIndexCrudAction = $this->adminUrlGenerator->setAll(array_merge($currentUrlQueryParams, [
            EA::DASHBOARD_CONTROLLER_FQCN => $request->attributes->get(EA::DASHBOARD_CONTROLLER_FQCN),
            EA::CRUD_CONTROLLER_FQCN => $crudControllerFqcn,
            EA::CRUD_ACTION => Action::INDEX,
        ]))->generateUrl();

        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedPrettyUrlsMenuItem($subItems, $request));
            }

            // compare the ending of the URL instead of a strict equality because link URLs can be absolute URLs
            if ('' !== $menuItemDto->getLinkUrl() && str_ends_with($currentUrlWithIndexCrudAction, $menuItemDto->getLinkUrl())) {
                $menuItemDto->setSelected(true);

                return $menuItems;
            }
        }

        return $menuItems;
    }

    /**
     * Given the full list of menu items, this method finds which item should be
     * marked as expanded because any of its items is currently selected and
     * updates it.
     *
     * @param MenuItemDto[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function doMarkExpandedMenuItem(array $menuItems): array
    {
        foreach ($menuItems as $menuItemDto) {
            if ([] === $menuSubitems = $menuItemDto->getSubItems()) {
                continue;
            }

            foreach ($menuSubitems as $submenuItem) {
                if ($submenuItem->isSelected()) {
                    $menuItemDto->setExpanded(true);

                    break;
                }
            }
        }

        return $menuItems;
    }

    /**
     * It scans the full list of menu items to find which controllers and actions
     * are linked from the menu. The output is something like:
     * [
     *     'App\Controller\Admin\BlogPostCrudController' => ['index', 'new'],
     *     'App\Controller\Admin\BlogCategoryCrudController' => ['index'],
     *     // ...
     *     'App\Controller\Admin\UserCrudController' => ['index', 'new'],
     * ].
     *
     * @return array<string, array<string>>
     */
    private function getControllersAndActionsLinkedInTheMenu(array $menuItems): array
    {
        $controllersAndActionsLinkedInTheMenu = [];
        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $controllersAndActionsLinkedInTheMenu = array_merge_recursive($controllersAndActionsLinkedInTheMenu, $this->getControllersAndActionsLinkedInTheMenu($subItems));

                continue;
            }

            $menuItemQueryString = null === $menuItemDto->getLinkUrl() ? null : parse_url($menuItemDto->getLinkUrl(), \PHP_URL_QUERY);
            if (null === $menuItemQueryString) {
                continue;
            }

            $menuItemQueryParameters = [];
            if (\is_string($menuItemQueryString)) {
                parse_str($menuItemQueryString, $menuItemQueryParameters);
            }

            $controllerFqcn = $menuItemQueryParameters[EA::CRUD_CONTROLLER_FQCN] ?? null;
            $crudAction = $menuItemQueryParameters[EA::CRUD_ACTION] ?? null;
            if (null === $controllerFqcn || null === $crudAction) {
                continue;
            }

            if (isset($controllersAndActionsLinkedInTheMenu[$controllerFqcn])) {
                $controllersAndActionsLinkedInTheMenu[$controllerFqcn][] = $crudAction;
            } else {
                $controllersAndActionsLinkedInTheMenu[$controllerFqcn] = [$crudAction];
            }
        }

        return $controllersAndActionsLinkedInTheMenu;
    }

    /*
     * Sorts an array recursively by its keys. This is needed because some values
     * of the array with the query string parameters can be arrays too, and we must
     * sort those before the comparison.
     */
    private function recursiveKsort(&$array): void
    {
        if (!\is_array($array)) {
            return;
        }

        ksort($array);

        foreach ($array as &$value) {
            if (\is_array($value)) {
                $this->recursiveKsort($value);
            }
        }
    }

    /**
     * Removes from the given list of query parameters all the parameters that
     * should be ignored when deciding if some menu item matches the current page
     * (such as the applied filters or sorting, the listing page number, etc.).
     */
    private function filterIrrelevantQueryParameters(array $queryStringParameters): array
    {
        $paramsToRemove = [EA::REFERRER, EA::PAGE, EA::FILTERS, EA::SORT];

        return array_filter($queryStringParameters, static fn ($k) => !\in_array($k, $paramsToRemove, true), \ARRAY_FILTER_USE_KEY);
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemMatcherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class MenuItemMatcher implements MenuItemMatcherInterface
{
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
        $usePrettyUrls = true === (bool) $request->attributes->get(EA::ROUTE_CREATED_BY_EASYADMIN);
        if ($usePrettyUrls) {
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
        // 2) if no exact match is found, traverse all menu items again and try to find a partial match:
        // 2.1) strip the query string from the current URL and the menu item URL and look for a match
        // 2.2) if no match is found, strip the action from the request (e.g. /admin/post/new -> /admin/post) and try to match again

        // try to find an exact match with the current URL
        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedPrettyUrlsMenuItem($subItems, $request));
            }

            // compare the ending of the URL instead of a strict equality because link URLs can be absolute URLs
            if (str_ends_with($menuItemDto->getLinkUrl(), $request->getRequestUri())) {
                $menuItemDto->setSelected(true);

                return $menuItems;
            }
        }

        // if no exact match is found, try to find a partial match
        $currentRequestUri = $request->getUri();
        $currentRequestUriWithoutQueryString = parse_url($currentRequestUri, \PHP_URL_PATH);
        // remove the last segment of the URL but keep the trailing slash (e.g. /admin/post/new -> /admin/post/)
        $currentRequestUriWithoutAction = preg_replace('#/[^/]+$#', '', $currentRequestUriWithoutQueryString);
        // the edit URL is '/admin/foo/{id}/edit' so we need to remove the two last segments of the URL
        if (str_ends_with($currentRequestUriWithoutQueryString, '/edit')) {
            $currentRequestUriWithoutAction = preg_replace('#/[^/]+$#', '', $currentRequestUriWithoutAction);
        }

        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            $menuItemUrl = $menuItemDto->getLinkUrl();
            $menuItemUrlWithoutQueryString = parse_url($menuItemUrl, \PHP_URL_PATH);

            // compare the ending of the URL instead of a strict equality because link URLs can be absolute URLs
            if (str_ends_with($menuItemUrl, $currentRequestUriWithoutQueryString)
                || str_ends_with($menuItemUrl, $currentRequestUriWithoutAction)
                || str_ends_with($menuItemUrlWithoutQueryString, $currentRequestUriWithoutQueryString)
                || str_ends_with($menuItemUrlWithoutQueryString, $currentRequestUriWithoutAction)) {
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

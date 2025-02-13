<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemMatcherInterface;
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
        $menuItems = $this->doMarkSelectedMenuItem($menuItems, $request);
        $menuItems = $this->doMarkExpandedMenuItem($menuItems);

        return $menuItems;
    }

    /**
     * @param MenuItemDto[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function doMarkSelectedMenuItem(array $menuItems, Request $request): array
    {
        // the menu-item matching is a multi-phase process:
        // 1) check all menu items for an exact match with the current URL
        // 2) if no match, check again with the current URL action changed to 'index'
        // 3) if still no match, check again with the current URL action changed to 'index' and no query parameters
        $currentUrlWithoutHost = $request->getPathInfo();
        $currentUrlQueryParams = $request->query->all();
        unset($currentUrlQueryParams[EA::SORT], $currentUrlQueryParams[EA::PAGE], $currentUrlQueryParams[EA::QUERY], $currentUrlQueryParams[EA::FILTERS]);
        // sort them because menu items always have their query parameters sorted
        ksort($currentUrlQueryParams);

        $currentUrlWithoutHostAndWithNormalizedQueryString = $currentUrlWithoutHost;
        if ([] !== $currentUrlQueryParams) {
            $currentUrlWithoutHostAndWithNormalizedQueryString .= '?'.http_build_query($currentUrlQueryParams);
        }

        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedMenuItem($subItems, $request));
            }

            // remove host part from the menu item link URL
            $urlParts = parse_url($menuItemDto->getLinkUrl());
            $menuItemUrlWithoutHost = $urlParts['path'] ?? '';
            if (\array_key_exists('query', $urlParts)) {
                $menuItemUrlWithoutHost .= '?'.$urlParts['query'];
            }
            if (\array_key_exists('fragment', $urlParts)) {
                $menuItemUrlWithoutHost .= '#'.$urlParts['fragment'];
            }

            if ($menuItemUrlWithoutHost === $currentUrlWithoutHostAndWithNormalizedQueryString) {
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

        if ($this->matchUrlInMenuItems($currentUrlWithIndexCrudAction, $menuItems, $request)) {
            return $menuItems;
        }

        $currentUrlWithIndexCrudActionAndWithoutQueryParams = $this->adminUrlGenerator->unsetAll()->setAll([
            EA::DASHBOARD_CONTROLLER_FQCN => $request->attributes->get(EA::DASHBOARD_CONTROLLER_FQCN),
            EA::CRUD_CONTROLLER_FQCN => $crudControllerFqcn,
            EA::CRUD_ACTION => Action::INDEX,
        ])->generateUrl();

        $this->matchUrlInMenuItems($currentUrlWithIndexCrudActionAndWithoutQueryParams, $menuItems, $request);

        return $menuItems;
    }

    /**
     * @param MenuItemDto[] $menuItems
     */
    private function matchUrlInMenuItems(string $urlToMatch, array $menuItems, Request $request): bool
    {
        foreach ($menuItems as $menuItemDto) {
            if ($menuItemDto->isMenuSection()) {
                continue;
            }

            if ([] !== $subItems = $menuItemDto->getSubItems()) {
                $menuItemDto->setSubItems($this->doMarkSelectedMenuItem($subItems, $request));
            }

            // compare the ending of the URL instead of a strict equality because link URLs can be absolute URLs
            if ('' !== $menuItemDto->getLinkUrl() && str_ends_with($urlToMatch, $menuItemDto->getLinkUrl())) {
                $menuItemDto->setSelected(true);

                return true;
            }
        }

        return false;
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
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\AccessImpersonationMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\DashboardMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\ExitImpersonationMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\LogoutMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\RouteMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\UrlMenuItem;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class MenuItem
{
    private function __construct()
    {
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToCrud(TranslatableInterface|string $label, ?string $icon, string $entityFqcn): CrudMenuItem
    {
        return new CrudMenuItem($label, $icon, $entityFqcn);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToDashboard(TranslatableInterface|string $label, ?string $icon = null): DashboardMenuItem
    {
        return new DashboardMenuItem($label, $icon);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToExitImpersonation(TranslatableInterface|string $label, ?string $icon = null): ExitImpersonationMenuItem
    {
        return new ExitImpersonationMenuItem($label, $icon);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToAccessImpersonation(TranslatableInterface|string $label, ?string $icon = null): AccessImpersonationMenuItem
    {
        return new AccessImpersonationMenuItem($label, $icon);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToLogout(TranslatableInterface|string $label, ?string $icon = null): LogoutMenuItem
    {
        return new LogoutMenuItem($label, $icon);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToRoute(TranslatableInterface|string $label, ?string $icon, string $routeName, array $routeParameters = []): RouteMenuItem
    {
        return new RouteMenuItem($label, $icon, $routeName, $routeParameters);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function linkToUrl(TranslatableInterface|string $label, ?string $icon, string $url): UrlMenuItem
    {
        return new UrlMenuItem($label, $icon, $url);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function section(TranslatableInterface|string|null $label = null, ?string $icon = null): SectionMenuItem
    {
        return new SectionMenuItem($label, $icon);
    }

    /**
     * @param string|null $icon The full CSS classes of the FontAwesome icon to render (see https://fontawesome.com/v6/search?m=free)
     */
    public static function subMenu(TranslatableInterface|string $label, ?string $icon = null): SubMenuItem
    {
        return new SubMenuItem($label, $icon);
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Attribute;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AdminDashboard
{
    public function __construct(
        /** @var array<string, array{routeName?: string, routePath?: string}>|null */
        public ?array $routes = null,
        /** @var class-string[]|null $allowedControllers If defined, only these CRUD controllers will have a route defined for them */
        public ?array $allowedControllers = null,
        /** @var class-string[]|null $deniedControllers If defined, all CRUD controllers will have a route defined for them except these ones */
        public ?array $deniedControllers = null,
    ) {
    }
}

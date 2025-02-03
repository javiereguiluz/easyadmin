<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Attribute;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AdminDashboard
{
    public function __construct(
        /**
         * @param string|null $routePath The path of the Symfony route that will be created for the dashboard (e.g. '/admin)
         */
        public /* ?string */ $routePath = null,
        /**
         * @param string|null $routeName The name of the Symfony route that will be created for the dashboard (e.g. 'admin')
         */
        public /* ?string */ $routeName = null,
        /**
         * @param array{
         *     requirements?: array,
         *     options?: array,
         *     defaults?: array,
         *     host?: string,
         *     methods?: array|string,
         *     schemes?: array|string,
         *     condition?: string,
         *     locale?: string,
         *     format?: string,
         *     utf8?: bool,
         *     stateless?: bool,
         * } $routeOptions The configuration used when creating the Symfony route for the dashboard (these values are passed "as is" without any additional validation)
         */
        public array $routeOptions = [],
        /** @var array<string, array{routeName?: string, routePath?: string}>|null Allows to change the default route name and/or path of the CRUD actions for this dashboard */
        public ?array $routes = null,
        /** @var class-string[]|null $allowedControllers If defined, only these CRUD controllers will have a route defined for them */
        public ?array $allowedControllers = null,
        /** @var class-string[]|null $deniedControllers If defined, all CRUD controllers will have a route defined for them except these ones */
        public ?array $deniedControllers = null,
    ) {
        // when this attribute was first created, the $routes, $allowedControllers, and $deniedControllers
        // were the first and only arguments of the class; now we added $routePath and $routeName as the
        // first arguments, so we need to move the values of the old arguments to the new ones
        if (\func_num_args() > 0 && \is_array(func_get_arg(0))) {
            $this->routes = func_get_arg(0);
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.24.0',
                'Passing $routes as the first argument of the "%s" attribute is deprecated and will no longer work in EasyAdmin 5.0.0. Pass the routes as the fourth argument or, better, use the \'routes:\' named argument.',
                __CLASS__,
            );
        }
        if (\func_num_args() > 1 && \is_array(func_get_arg(1))) {
            $this->allowedControllers = func_get_arg(1);
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.24.0',
                'Passing $allowedControllers as the second argument of the "%s" attribute is deprecated and will no longer work in EasyAdmin 5.0.0. Pass the allowed controllers as the fifth argument or, better, use the \'allowedControllers:\' named argument.',
                __CLASS__,
            );
        }
        if (\func_num_args() > 2 && \is_array(func_get_arg(0)) && \is_array(func_get_arg(1)) && \is_array(func_get_arg(2))) {
            $this->deniedControllers = func_get_arg(2);
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.24.0',
                'Passing $deniedControllers as the third argument of the "%s" attribute is deprecated and will no longer work in EasyAdmin 5.0.0. Pass the denied controllers as the sixth argument or, better, use the \'deniedControllers:\' named argument.',
                __CLASS__,
            );
        }
    }
}

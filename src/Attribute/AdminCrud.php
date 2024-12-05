<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Attribute;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AdminCrud
{
    public function __construct(
        public ?string $routePath = null,
        public ?string $routeName = null,
    ) {
    }
}

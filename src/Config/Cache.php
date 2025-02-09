<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

class Cache
{
    public const ROUTE_NAME_TO_ATTRIBUTES = 'easyadmin.routes.route_to_fqcn';
    public const ROUTE_ATTRIBUTES_TO_NAME = 'easyadmin.routes.fqcn_to_route';
    public const DASHBOARD_FQCN_TO_ROUTE = 'easyadmin.routes.controller_fqcn_to_dashboard_route';
    public const CRUD_FQCN_TO_ENTITY_FQCN = 'easyadmin.crud.controller_fqcn_to_entity_fqcn';
    public const ENTITY_FQCN_TO_CRUD_FQCN = 'easyadmin.crud.entity_fqcn_to_controller_fqcn';
}

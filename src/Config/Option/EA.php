<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config\Option;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EA
{
    public const BATCH_ACTION_NAME = 'batchActionName';
    public const BATCH_ACTION_URL = 'batchActionUrl';
    public const BATCH_ACTION_CSRF_TOKEN = 'batchActionCsrfToken';
    public const BATCH_ACTION_ENTITY_IDS = 'batchActionEntityIds';
    public const CONTEXT_NAME = 'eaContext';
    public const CONTEXT_REQUEST_ATTRIBUTE = 'easyadmin_context';
    public const CRUD_ACTION = 'crudAction';
    public const CRUD_CONTROLLER_FQCN = 'crudControllerFqcn';
    public const DASHBOARD_CONTROLLER_FQCN = 'dashboardControllerFqcn';
    public const ENTITY_FQCN = 'entityFqcn';
    public const ENTITY_ID = 'entityId';
    public const FILTERS = 'filters';
    /** @deprecated this parameter is no longer used because menu items are now highlighted automatically */
    public const MENU_INDEX = 'menuIndex';
    public const PAGE = 'page';
    public const QUERY = 'query';
    /** @deprecated this parameter is no longer used because the referrer URL is now generated automatically */
    public const REFERRER = 'referrer';
    public const ROUTE_NAME = 'routeName';
    public const ROUTE_PARAMS = 'routeParams';
    public const ROUTE_CREATED_BY_EASYADMIN = 'routeCreatedByEasyAdmin';
    public const SORT = 'sort';
    /** @deprecated this parameter is no longer used because menu items are now highlighted automatically */
    public const SUBMENU_INDEX = 'submenuIndex';
    public const URL_SIGNATURE = 'signature';
}

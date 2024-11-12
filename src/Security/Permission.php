<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Security;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class Permission
{
    public const EA_ACCESS_ENTITY = 'EA_ACCESS_ENTITY';
    public const EA_EXECUTE_ACTION = 'EA_EXECUTE_ACTION';
    public const EA_VIEW_MENU_ITEM = 'EA_VIEW_MENU_ITEM';
    public const EA_VIEW_FIELD = 'EA_VIEW_FIELD';
    public const EA_EXIT_IMPERSONATION = 'EA_EXIT_IMPERSONATION';
    public const EA_CAN_SWITCH_USER = 'EA_CAN_SWITCH_USER';

    public static function exists(?string $permissionName): bool
    {
        if (null === $permissionName) {
            return false;
        }

        return \defined('self::'.$permissionName);
    }
}

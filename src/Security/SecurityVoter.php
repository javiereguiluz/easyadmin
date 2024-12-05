<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Security;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/*
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class SecurityVoter extends Voter
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    protected function supports(string $permissionName, mixed $subject): bool
    {
        return $this->supportsAttribute($permissionName);
    }

    public function supportsAttribute(string $permissionName): bool
    {
        return Permission::exists($permissionName);
    }

    protected function voteOnAttribute($permissionName, $subject, TokenInterface $token): bool
    {
        if (Permission::EA_VIEW_MENU_ITEM === $permissionName) {
            return $this->voteOnViewMenuItemPermission($subject);
        }

        if (Permission::EA_EXECUTE_ACTION === $permissionName) {
            return $this->voteOnExecuteActionPermission($this->adminContextProvider->getContext()->getCrud(), $subject['action'] ?? null, $subject['entity'] ?? null, $subject['entityFqcn'] ?? null);
        }

        if (Permission::EA_VIEW_FIELD === $permissionName) {
            return $this->voteOnViewPropertyPermission($subject);
        }

        if (Permission::EA_ACCESS_ENTITY === $permissionName) {
            return $this->voteOnViewEntityPermission($subject);
        }

        if (Permission::EA_EXIT_IMPERSONATION === $permissionName) {
            return $this->voteOnExitImpersonationPermission();
        }

        return true;
    }

    private function voteOnViewMenuItemPermission(MenuItemDto $menuItemDto): bool
    {
        // users can see the menu item if they have the permission required by the menu item
        return $this->authorizationChecker->isGranted($menuItemDto->getPermission(), $menuItemDto);
    }

    private function voteOnExecuteActionPermission(CrudDto $crudDto, ActionDto|string $actionNameOrDto, ?EntityDto $entityDto, ?string $entityFqcn): bool
    {
        // users can run the Crud action if:
        // * they have the required permission to execute the action on the given entity instance
        // * the action is not disabled

        $actionName = \is_string($actionNameOrDto) ? $actionNameOrDto : $actionNameOrDto->getName();

        $actionPermission = $crudDto->getActionsConfig()->getActionPermissions()[$actionName] ?? null;
        $disabledActionNames = $crudDto->getActionsConfig()->getDisabledActions();

        $subject = $entityDto?->getInstance() ?? $entityFqcn;

        return !\in_array($actionName, $disabledActionNames, true) && $this->authorizationChecker->isGranted($actionPermission, $subject);
    }

    private function voteOnViewPropertyPermission(FieldDto $field): bool
    {
        // users can see the field if they have the permission required by the field
        return $this->authorizationChecker->isGranted($field->getPermission(), $field);
    }

    private function voteOnViewEntityPermission(EntityDto $entityDto): bool
    {
        // users can see the entity if they have the required permission on the specific entity instance
        return $this->authorizationChecker->isGranted($entityDto->getPermission(), $entityDto->getInstance());
    }

    private function voteOnExitImpersonationPermission(): bool
    {
        // users can exit impersonation if they are currently impersonating another user.
        // In Symfony, that means that current user has the special impersonator permission
        if (\defined('Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter::IS_IMPERSONATOR')) {
            $impersonatorPermission = 'IS_IMPERSONATOR';
        } else {
            $impersonatorPermission = 'ROLE_PREVIOUS_ADMIN';
        }

        return $this->authorizationChecker->isGranted($impersonatorPermission);
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class ActionConfigDto
{
    private ?string $pageName = null;
    /**
     * @var array<string,array<string,ActionDto>>
     */
    private array $actions = [
        Crud::PAGE_DETAIL => [],
        Crud::PAGE_EDIT => [],
        Crud::PAGE_INDEX => [],
        Crud::PAGE_NEW => [],
    ];
    /** @var string[] */
    private array $disabledActions = [];
    /** @var string[]|Expression[] */
    private array $actionPermissions = [];

    public function __construct()
    {
    }

    public function __clone()
    {
        foreach ($this->actions as $pageName => $actions) {
            foreach ($actions as $actionName => $actionDto) {
                $this->actions[$pageName][$actionName] = clone $actionDto;
            }
        }
    }

    public function setPageName(?string $pageName): void
    {
        $this->pageName = $pageName;
    }

    public function setActionPermission(string $actionName, string|Expression $permission): void
    {
        $this->actionPermissions[$actionName] = $permission;
    }

    public function setActionPermissions(array $permissions): void
    {
        $this->actionPermissions = $permissions;
    }

    public function prependAction(string $pageName, ActionDto $actionDto): void
    {
        $this->actions[$pageName] = array_merge([$actionDto->getName() => $actionDto], $this->actions[$pageName]);
    }

    public function appendAction(string $pageName, ActionDto $actionDto): void
    {
        $this->actions[$pageName] = array_merge($this->actions[$pageName], [$actionDto->getName() => $actionDto]);
    }

    public function setAction(string $pageName, ActionDto $actionDto): void
    {
        $this->actions[$pageName][$actionDto->getName()] = $actionDto;
    }

    public function getAction(string $pageName, string $actionName): ?ActionDto
    {
        return $this->actions[$pageName][$actionName] ?? null;
    }

    public function removeAction(string $pageName, string $actionName): void
    {
        unset($this->actions[$pageName][$actionName]);
    }

    public function reorderActions(string $pageName, array $orderedActionNames): void
    {
        $orderedActions = [];
        foreach ($orderedActionNames as $actionName) {
            $orderedActions[$actionName] = $this->actions[$pageName][$actionName];
        }

        $this->actions[$pageName] = $orderedActions;
    }

    public function disableActions(array $actionNames): void
    {
        foreach ($actionNames as $actionName) {
            if (!\in_array($actionName, $this->disabledActions, true)) {
                $this->disabledActions[] = $actionName;
            }
        }
    }

    public function getActions(): ActionCollection|array
    {
        return null === $this->pageName ? $this->actions : ActionCollection::new($this->actions[$this->pageName]);
    }

    /**
     * @param ActionDto[] $newActions
     */
    public function setActions(string $pageName, array $newActions): void
    {
        $this->actions[$pageName] = $newActions;
    }

    public function getDisabledActions(): array
    {
        return $this->disabledActions;
    }

    public function getActionPermissions(): array
    {
        return $this->actionPermissions;
    }
}

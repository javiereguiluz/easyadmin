<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDto;
use Symfony\Component\ExpressionLanguage\Expression;
use function Symfony\Component\Translation\t;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class Actions
{
    private ActionConfigDto $dto;

    private function __construct(ActionConfigDto $actionConfigDto)
    {
        $this->dto = $actionConfigDto;
    }

    public static function new(): self
    {
        $dto = new ActionConfigDto();

        return new self($dto);
    }

    public function add(string $pageName, Action|string $actionNameOrObject): self
    {
        return $this->doAddAction($pageName, $actionNameOrObject);
    }

    public function addBatchAction(Action|string $actionNameOrObject): self
    {
        return $this->doAddAction(Crud::PAGE_INDEX, $actionNameOrObject, true);
    }

    public function set(string $pageName, Action|string $actionNameOrObject): self
    {
        $action = \is_string($actionNameOrObject) ? $this->createBuiltInAction($pageName, $actionNameOrObject) : $actionNameOrObject;

        $this->dto->appendAction($pageName, $action->getAsDto());

        return $this;
    }

    public function update(string $pageName, string $actionName, callable $callable): self
    {
        if (null === $actionDto = $this->dto->getAction($pageName, $actionName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" action does not exist in the "%s" page, so you cannot update it. Instead, add the action with the "add()" method.', $actionName, $pageName));
        }

        $action = $actionDto->getAsConfigObject();
        /** @var Action $action */
        $action = $callable($action);
        $this->dto->setAction($pageName, $action->getAsDto());

        return $this;
    }

    public function remove(string $pageName, string $actionName): self
    {
        if (null === $this->dto->getAction($pageName, $actionName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" action does not exist in the "%s" page, so you cannot remove it.', $actionName, $pageName));
        }

        $this->dto->removeAction($pageName, $actionName);
        // if 'delete' is removed, 'batch delete' is removed automatically (but the
        // opposite doesn't happen). This is the most common case, but user can re-add
        // the 'batch delete' action if needed manually
        if (Action::DELETE === $actionName) {
            $this->dto->removeAction($pageName, Action::BATCH_DELETE);
        }

        return $this;
    }

    public function reorder(string $pageName, array $orderedActionNames): self
    {
        $newActionOrder = [];
        $currentActions = $this->dto->getActions();
        foreach ($orderedActionNames as $actionName) {
            if (!\array_key_exists($actionName, $currentActions[$pageName])) {
                throw new \InvalidArgumentException(sprintf('The "%s" action does not exist in the "%s" page, so you cannot set its order.', $actionName, $pageName));
            }

            $newActionOrder[] = $actionName;
        }

        // add the remaining actions that weren't ordered explicitly. This allows
        // user to only configure the actions they want to see first and rely on the
        // existing order for the rest of actions
        foreach ($currentActions[$pageName] as $actionName => $action) {
            if (!\in_array($actionName, $newActionOrder, true)) {
                $newActionOrder[] = $actionName;
            }
        }

        $this->dto->reorderActions($pageName, $newActionOrder);

        return $this;
    }

    public function setPermission(string $actionName, string|Expression $permission): self
    {
        $this->dto->setActionPermission($actionName, $permission);

        return $this;
    }

    /**
     * @param array $permissions Syntax: ['actionName' => 'actionPermission', ...]
     */
    public function setPermissions(array $permissions): self
    {
        $this->dto->setActionPermissions($permissions);

        return $this;
    }

    public function disable(string ...$disabledActionNames): self
    {
        // if 'delete' is disabled, 'batch delete' is disabled automatically (but the
        // opposite doesn't happen). This is the most common case, but user can re-enable
        // the 'batch delete' action if needed manually
        if (\in_array(Action::DELETE, $disabledActionNames, true)) {
            $disabledActionNames[] = Action::BATCH_DELETE;
        }

        $this->dto->disableActions($disabledActionNames);

        return $this;
    }

    public function getAsDto(?string $pageName): ActionConfigDto
    {
        $this->dto->setPageName($pageName);

        return $this->dto;
    }

    /**
     * The $pageName is needed because sometimes the same action has different config
     * depending on where it's displayed (to display an icon in 'detail' but not in 'index', etc.).
     */
    private function createBuiltInAction(string $pageName, string $actionName): Action
    {
        if (Action::BATCH_DELETE === $actionName) {
            return Action::new(Action::BATCH_DELETE, t('action.delete', domain: 'EasyAdminBundle'), null)
                ->linkToCrudAction(Action::BATCH_DELETE)
                ->setCssClass('action-'.Action::BATCH_DELETE)
                ->addCssClass('btn btn-danger pr-0');
        }

        if (Action::NEW === $actionName) {
            return Action::new(Action::NEW, t('action.new', domain: 'EasyAdminBundle'), null)
                ->createAsGlobalAction()
                ->linkToCrudAction(Action::NEW)
                ->setCssClass('action-'.Action::NEW)
                ->addCssClass('btn btn-primary');
        }

        if (Action::EDIT === $actionName) {
            return Action::new(Action::EDIT, t('action.edit', domain: 'EasyAdminBundle'), null)
                ->linkToCrudAction(Action::EDIT)
                ->setCssClass('action-'.Action::EDIT)
                ->addCssClass(Crud::PAGE_DETAIL === $pageName ? 'btn btn-primary' : '');
        }

        if (Action::DETAIL === $actionName) {
            return Action::new(Action::DETAIL, t('action.detail', domain: 'EasyAdminBundle'))
                ->linkToCrudAction(Action::DETAIL)
                ->setCssClass('action-'.Action::DETAIL)
                ->addCssClass(Crud::PAGE_EDIT === $pageName ? 'btn btn-secondary' : '');
        }

        if (Action::INDEX === $actionName) {
            return Action::new(Action::INDEX, t('action.index', domain: 'EasyAdminBundle'))
                ->linkToCrudAction(Action::INDEX)
                ->setCssClass('action-'.Action::INDEX)
                ->addCssClass(\in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_EDIT, Crud::PAGE_NEW], true) ? 'btn btn-secondary' : '');
        }

        if (Action::DELETE === $actionName) {
            $cssClass = \in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_EDIT], true) ? 'btn btn-secondary pr-0 text-danger' : 'text-danger';

            return Action::new(Action::DELETE, t('action.delete', domain: 'EasyAdminBundle'), Crud::PAGE_INDEX === $pageName ? null : 'internal:delete')
                ->linkToCrudAction(Action::DELETE)
                ->setCssClass('action-'.Action::DELETE)
                ->addCssClass($cssClass);
        }

        if (Action::SAVE_AND_RETURN === $actionName) {
            return Action::new(Action::SAVE_AND_RETURN, t(Crud::PAGE_EDIT === $pageName ? 'action.save' : 'action.create', domain: 'EasyAdminBundle'))
                ->setCssClass('action-'.Action::SAVE_AND_RETURN)
                ->addCssClass('btn btn-primary action-save')
                ->displayAsButton()
                ->setHtmlAttributes(['type' => 'submit', 'name' => 'ea[newForm][btn]', 'value' => $actionName])
                ->linkToCrudAction(Crud::PAGE_EDIT === $pageName ? Action::EDIT : Action::NEW);
        }

        if (Action::SAVE_AND_CONTINUE === $actionName) {
            return Action::new(Action::SAVE_AND_CONTINUE, t(Crud::PAGE_EDIT === $pageName ? 'action.save_and_continue' : 'action.create_and_continue', domain: 'EasyAdminBundle'), 'internal:edit')
                ->setCssClass('action-'.Action::SAVE_AND_CONTINUE)
                ->addCssClass('btn btn-secondary action-save')
                ->displayAsButton()
                ->setHtmlAttributes(['type' => 'submit', 'name' => 'ea[newForm][btn]', 'value' => $actionName])
                ->linkToCrudAction(Crud::PAGE_EDIT === $pageName ? Action::EDIT : Action::NEW);
        }

        if (Action::SAVE_AND_ADD_ANOTHER === $actionName) {
            return Action::new(Action::SAVE_AND_ADD_ANOTHER, t('action.create_and_add_another', domain: 'EasyAdminBundle'))
                ->setCssClass('action-'.Action::SAVE_AND_ADD_ANOTHER)
                ->addCssClass('btn btn-secondary action-save')
                ->displayAsButton()
                ->setHtmlAttributes(['type' => 'submit', 'name' => 'ea[newForm][btn]', 'value' => $actionName])
                ->linkToCrudAction(Action::NEW);
        }

        throw new \InvalidArgumentException(sprintf('The "%s" action is not a built-in action, so you can\'t add or configure it via its name. Either refer to one of the built-in actions or create a custom action called "%s".', $actionName, $actionName));
    }

    private function doAddAction(string $pageName, Action|string $actionNameOrObject, bool $isBatchAction = false): self
    {
        $actionName = \is_string($actionNameOrObject) ? $actionNameOrObject : (string) $actionNameOrObject;
        $action = \is_string($actionNameOrObject) ? $this->createBuiltInAction($pageName, $actionNameOrObject) : $actionNameOrObject;

        if (null !== $this->dto->getAction($pageName, $actionName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" action already exists in the "%s" page, so you can\'t add it again. Instead, you can use the "updateAction()" method to update any options of an existing action.', $actionName, $pageName));
        }

        $actionDto = $action->getAsDto();
        if ($isBatchAction) {
            $actionDto->setType(Action::TYPE_BATCH);
        }

        if (Crud::PAGE_INDEX === $pageName && Action::DELETE === $actionName) {
            $this->dto->prependAction($pageName, $actionDto);
        } else {
            $this->dto->appendAction($pageName, $actionDto);
        }

        return $this;
    }
}

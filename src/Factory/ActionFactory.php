<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\ActionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\CrudInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Security\PermissionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Translation\TranslatableMessageBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use function Symfony\Component\Translation\t;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ActionFactory implements ActionFactoryInterface
{
    private AdminContextProvider $adminContextProvider;
    private AuthorizationCheckerInterface $authChecker;
    private AdminUrlGenerator $adminUrlGenerator;
    private ?CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(AdminContextProviderInterface $adminContextProvider, AuthorizationCheckerInterface $authChecker, AdminUrlGeneratorInterface $adminUrlGenerator, ?CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->authChecker = $authChecker;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function processEntityActions(EntityDtoInterface $entityDto, ActionConfigDtoInterface $actionsDto): void
    {
        $currentPage = $this->adminContextProvider->getContext()->getCrud()->getCurrentPage();
        $entityActions = [];
        foreach ($actionsDto->getActions()->all() as $actionDto) {
            if (!$actionDto->isEntityAction()) {
                continue;
            }

            if (false === $this->authChecker->isGranted(PermissionInterface::EA_EXECUTE_ACTION, ['action' => $actionDto, 'entity' => $entityDto])) {
                continue;
            }

            if (false === $actionDto->shouldBeDisplayedFor($entityDto)) {
                continue;
            }

            // if CSS class hasn't been overridden, apply the default ones
            if ('' === $actionDto->getCssClass()) {
                $defaultCssClass = 'action-'.$actionDto->getName();
                if (CrudInterface::PAGE_INDEX !== $currentPage) {
                    $defaultCssClass .= ' btn';
                }

                $actionDto->setCssClass($defaultCssClass);
            }

            // these are the additional custom CSS classes defined via addCssClass()
            // which are always appended to the CSS classes (default ones or custom ones)
            if ('' !== $addedCssClass = $actionDto->getAddedCssClass()) {
                $actionDto->setCssClass($actionDto->getCssClass().' '.$addedCssClass);
            }

            $entityActions[] = $this->processAction($currentPage, $actionDto, $entityDto);
        }

        $entityDto->setActions(ActionCollection::new($entityActions));
    }

    public function processGlobalActions(?ActionConfigDtoInterface $actionsDto = null): ActionCollection
    {
        if (null === $actionsDto) {
            $actionsDto = $this->adminContextProvider->getContext()->getCrud()->getActionsConfig();
        }

        $currentPage = $this->adminContextProvider->getContext()->getCrud()->getCurrentPage();
        $globalActions = [];
        foreach ($actionsDto->getActions()->all() as $actionDto) {
            if (!$actionDto->isGlobalAction() && !$actionDto->isBatchAction()) {
                continue;
            }

            if (false === $this->authChecker->isGranted(PermissionInterface::EA_EXECUTE_ACTION, ['action' => $actionDto, 'entity' => null])) {
                continue;
            }

            if (CrudInterface::PAGE_INDEX !== $currentPage && $actionDto->isBatchAction()) {
                throw new \RuntimeException(sprintf('Batch actions can be added only to the "index" page, but the "%s" batch action is defined in the "%s" page.', $actionDto->getName(), $currentPage));
            }

            if ('' === $actionDto->getCssClass()) {
                $actionDto->setCssClass('btn action-'.$actionDto->getName());
            }

            $globalActions[] = $this->processAction($currentPage, $actionDto);
        }

        return ActionCollection::new($globalActions);
    }

    private function processAction(string $pageName, ActionDtoInterface $actionDto, ?EntityDtoInterface $entityDto = null): ActionDtoInterface
    {
        $adminContext = $this->adminContextProvider->getContext();
        $translationDomain = $adminContext->getI18n()->getTranslationDomain();
        $defaultTranslationParameters = $adminContext->getI18n()->getTranslationParameters();
        $currentPage = $adminContext->getCrud()->getCurrentPage();

        $actionDto->setHtmlAttribute('data-action-name', $actionDto->getName());

        if (false === $actionDto->getLabel()) {
            $actionDto->setHtmlAttribute('title', $actionDto->getName());
        } elseif (!$actionDto->getLabel() instanceof TranslatableInterface) {
            $translationParameters = array_merge(
                $defaultTranslationParameters,
                $actionDto->getTranslationParameters()
            );
            $label = $actionDto->getLabel();
            $translatableActionLabel = (null === $label || '' === $label) ? $label : t($label, $translationParameters, $translationDomain);
            $actionDto->setLabel($translatableActionLabel);
        } else {
            $actionDto->setLabel(TranslatableMessageBuilder::withParameters($actionDto->getLabel(), $defaultTranslationParameters));
        }

        $defaultTemplatePath = $adminContext->getTemplatePath('crud/action');
        $actionDto->setTemplatePath($actionDto->getTemplatePath() ?? $defaultTemplatePath);

        $actionDto->setLinkUrl($this->generateActionUrl($currentPage, $adminContext->getRequest(), $actionDto, $entityDto));

        if (!$actionDto->isGlobalAction() && \in_array($pageName, [CrudInterface::PAGE_EDIT, CrudInterface::PAGE_NEW], true)) {
            $actionDto->setHtmlAttribute('form', sprintf('%s-%s-form', $pageName, $entityDto->getName()));
        }

        if (ActionInterface::DELETE === $actionDto->getName()) {
            $actionDto->addHtmlAttributes([
                'formaction' => $this->adminUrlGenerator->setAction(ActionInterface::DELETE)->setEntityId($entityDto->getPrimaryKeyValue())->removeReferrer()->generateUrl(),
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-delete',
            ]);
        }

        if ($actionDto->isBatchAction()) {
            $actionDto->addHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-batch-action',
                'data-action-csrf-token' => $this->csrfTokenManager?->getToken('ea-batch-action-'.$actionDto->getName()),
                'data-action-batch' => 'true',
                'data-entity-fqcn' => $adminContext->getCrud()->getEntityFqcn(),
                'data-action-url' => $actionDto->getLinkUrl(),
            ]);
        }

        return $actionDto;
    }

    private function generateActionUrl(string $currentAction, Request $request, ActionDtoInterface $actionDto, ?EntityDtoInterface $entityDto = null): string
    {
        $entityInstance = $entityDto?->getInstance();

        if (null !== $url = $actionDto->getUrl()) {
            if (\is_callable($url)) {
                return null !== $entityDto ? $url($entityInstance) : $url();
            }

            return $url;
        }

        if (null !== $routeName = $actionDto->getRouteName()) {
            $routeParameters = $actionDto->getRouteParameters();
            if (\is_callable($routeParameters) && null !== $entityInstance) {
                $routeParameters = $routeParameters($entityInstance);
            }

            return $this->adminUrlGenerator->unsetAll()->includeReferrer()->setRoute($routeName, $routeParameters)->generateUrl();
        }

        $requestParameters = [
            EA::CRUD_CONTROLLER_FQCN => $request->query->get(EA::CRUD_CONTROLLER_FQCN),
            EA::CRUD_ACTION => $actionDto->getCrudActionName(),
            EA::REFERRER => $this->generateReferrerUrl($request, $actionDto, $currentAction),
        ];

        if (\in_array($actionDto->getName(), [ActionInterface::INDEX, ActionInterface::NEW, ActionInterface::SAVE_AND_ADD_ANOTHER, ActionInterface::SAVE_AND_RETURN], true)) {
            $requestParameters[EA::ENTITY_ID] = null;
        } elseif (null !== $entityDto) {
            $requestParameters[EA::ENTITY_ID] = $entityDto->getPrimaryKeyValueAsString();
        }

        return $this->adminUrlGenerator->unsetAllExcept(EA::FILTERS, EA::PAGE)->setAll($requestParameters)->generateUrl();
    }

    private function generateReferrerUrl(Request $request, ActionDtoInterface $actionDto, string $currentAction): ?string
    {
        $nextAction = $actionDto->getName();

        if (ActionInterface::DETAIL === $currentAction) {
            if (ActionInterface::EDIT === $nextAction) {
                return $this->adminUrlGenerator->removeReferrer()->generateUrl();
            }
        }

        if (ActionInterface::INDEX === $currentAction) {
            return $this->adminUrlGenerator->removeReferrer()->generateUrl();
        }

        if (ActionInterface::NEW === $currentAction) {
            return null;
        }

        $referrer = $request->query->get(EA::REFERRER);
        $referrerParts = parse_url((string) $referrer);
        parse_str($referrerParts[EA::QUERY] ?? '', $referrerQueryStringVariables);
        $referrerCrudAction = $referrerQueryStringVariables[EA::CRUD_ACTION] ?? null;

        if (ActionInterface::EDIT === $currentAction) {
            if (\in_array($referrerCrudAction, [ActionInterface::INDEX, ActionInterface::DETAIL], true)) {
                return $referrer;
            }
        }

        return $this->adminUrlGenerator->removeReferrer()->generateUrl();
    }
}

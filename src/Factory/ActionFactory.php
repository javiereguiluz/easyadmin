<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Translation\TranslatableMessageBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use function Symfony\Component\Translation\t;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class ActionFactory
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly AdminUrlGeneratorInterface $adminUrlGenerator,
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null,
    ) {
    }

    public function processEntityActions(EntityDto $entityDto, ActionConfigDto $actionsDto): void
    {
        $currentPage = $this->adminContextProvider->getContext()->getCrud()->getCurrentPage();
        $entityActions = [];
        foreach ($actionsDto->getActions()->all() as $actionDto) {
            if (!$actionDto->isEntityAction()) {
                continue;
            }

            if (false === $this->authChecker->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => $actionDto, 'entity' => $entityDto])) {
                continue;
            }

            if (false === $actionDto->isDisplayed($entityDto)) {
                continue;
            }

            // if CSS class hasn't been overridden, apply the default ones
            if ('' === $actionDto->getCssClass()) {
                $defaultCssClass = 'action-'.$actionDto->getName();
                if (Crud::PAGE_INDEX !== $currentPage) {
                    $defaultCssClass .= ' btn';
                }

                $actionDto->setCssClass($defaultCssClass);
            }

            // these are the additional custom CSS classes defined via addCssClass()
            // which are always appended to the CSS classes (default ones or custom ones)
            if ('' !== $addedCssClass = $actionDto->getAddedCssClass()) {
                $actionDto->setCssClass($actionDto->getCssClass().' '.$addedCssClass);
            }

            $entityActions[$actionDto->getName()] = $this->processAction($currentPage, $actionDto, $entityDto);
        }

        $entityDto->setActions(ActionCollection::new($entityActions));
    }

    public function processGlobalActions(?ActionConfigDto $actionsDto = null): ActionCollection
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

            if (false === $this->authChecker->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => $actionDto, 'entity' => null])) {
                continue;
            }

            if (false === $actionDto->isDisplayed()) {
                continue;
            }

            if (Crud::PAGE_INDEX !== $currentPage && $actionDto->isBatchAction()) {
                throw new \RuntimeException(sprintf('Batch actions can be added only to the "index" page, but the "%s" batch action is defined in the "%s" page.', $actionDto->getName(), $currentPage));
            }

            // if CSS class hasn't been overridden, apply the default ones
            if ('' === $actionDto->getCssClass()) {
                $actionDto->setCssClass('btn action-'.$actionDto->getName());
            }

            // these are the additional custom CSS classes defined via addCssClass()
            // which are always appended to the CSS classes (default ones or custom ones)
            if ('' !== $addedCssClass = $actionDto->getAddedCssClass()) {
                $actionDto->setCssClass($actionDto->getCssClass().' '.$addedCssClass);
            }

            $globalActions[$actionDto->getName()] = $this->processAction($currentPage, $actionDto);
        }

        return ActionCollection::new($globalActions);
    }

    private function processAction(string $pageName, ActionDto $actionDto, ?EntityDto $entityDto = null): ActionDto
    {
        $adminContext = $this->adminContextProvider->getContext();
        $translationDomain = $adminContext->getI18n()->getTranslationDomain();
        $defaultTranslationParameters = $adminContext->getI18n()->getTranslationParameters();

        $actionDto->setHtmlAttribute('data-action-name', $actionDto->getName());

        $this->processActionLabel($actionDto, $entityDto, $translationDomain, $defaultTranslationParameters);

        $defaultTemplatePath = $adminContext->getTemplatePath('crud/action');
        $actionDto->setTemplatePath($actionDto->getTemplatePath() ?? $defaultTemplatePath);

        $actionDto->setLinkUrl($this->generateActionUrl($adminContext->getRequest(), $actionDto, $entityDto));

        if (!$actionDto->isGlobalAction() && \in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW], true)) {
            $actionDto->setHtmlAttribute('form', sprintf('%s-%s-form', $pageName, $entityDto->getName()));
        }

        if (Action::DELETE === $actionDto->getName()) {
            $actionDto->addHtmlAttributes([
                'formaction' => $this->adminUrlGenerator->setController($adminContext->getCrud()->getControllerFqcn())->setAction(Action::DELETE)->setEntityId($entityDto->getPrimaryKeyValue())->generateUrl(),
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

    private function processActionLabel(ActionDto $actionDto, ?EntityDto $entityDto, string $translationDomain, array $defaultTranslationParameters): void
    {
        $label = $actionDto->getLabel();

        // FALSE means that action doesn't show a visible label in the interface
        if (false === $label) {
            $actionDto->setHtmlAttribute('title', $actionDto->getName());

            return;
        }

        if (\is_callable($label) && $label instanceof \Closure) {
            $label = \call_user_func_array($label, array_filter([$entityDto?->getInstance()]));

            if (!\is_string($label) && !$label instanceof TranslatableInterface) {
                throw new \RuntimeException(sprintf('The callable used to define the label of the "%s" action label %s must return a string or a %s instance but it returned a(n) "%s" value instead.', $actionDto->getName(), null !== $entityDto ? 'in the "'.$entityDto->getName().'" entity' : '', TranslatableInterface::class, \gettype($label)));
            }
        }

        $translationParameters = array_merge($defaultTranslationParameters, $actionDto->getTranslationParameters());

        if ($label instanceof TranslatableInterface) {
            $actionDto->setLabel(TranslatableMessageBuilder::withParameters($label, $translationParameters));
        } else {
            $translatableActionLabel = (null === $label || '' === $label) ? $label : t($label, $translationParameters, $translationDomain);
            $actionDto->setLabel($translatableActionLabel);
        }
    }

    private function generateActionUrl(Request $request, ActionDto $actionDto, ?EntityDto $entityDto = null): string
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

            return $this->adminUrlGenerator->unsetAllExcept(EA::FILTERS, EA::PAGE, EA::QUERY, EA::SORT)->setRoute($routeName, $routeParameters)->generateUrl();
        }

        $requestParameters = [
            // when using pretty URLs, the data is in the request attributes instead of the query string
            EA::CRUD_CONTROLLER_FQCN => $request->attributes->get(EA::CRUD_CONTROLLER_FQCN) ?? $request->query->get(EA::CRUD_CONTROLLER_FQCN),
            EA::CRUD_ACTION => $actionDto->getCrudActionName(),
        ];

        if (\in_array($actionDto->getName(), [Action::INDEX, Action::NEW, Action::SAVE_AND_ADD_ANOTHER], true)) {
            $requestParameters[EA::ENTITY_ID] = null;
        } elseif (null !== $entityDto) {
            $requestParameters[EA::ENTITY_ID] = $entityDto->getPrimaryKeyValueAsString();
        }

        $urlParametersToKeep = [EA::FILTERS, EA::QUERY, EA::SORT, EA::BATCH_ACTION_CSRF_TOKEN, EA::BATCH_ACTION_ENTITY_IDS, EA::BATCH_ACTION_NAME, EA::BATCH_ACTION_URL];
        // when creating a new entity, keeping the selected page number is usually confusing:
        // 1. the user filters/searches/sorts/paginates the results and then creates a new entity
        // 2. if we keep the page number, when the backend returns to the listing, it's very probable
        //    that the user doesn't see the new entity, so they might think that it wasn't created
        // 3. if we keep the other parameters, it's probable that the new entity is shown (sometimes it won't)
        if (Action::NEW !== $actionDto->getName()) {
            $urlParametersToKeep[] = EA::PAGE;
        }

        return $this->adminUrlGenerator->unsetAllExcept(...$urlParametersToKeep)->setAll($requestParameters)->generateUrl();
    }
}

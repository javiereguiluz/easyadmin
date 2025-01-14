<?php

namespace EasyCorp\Bundle\EasyAdminBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/*
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
if (interface_exists(ValueResolverInterface::class)) {
    final class BatchActionDtoResolver implements ValueResolverInterface
    {
        public function __construct(
            private readonly AdminContextProviderInterface $adminContextProvider,
            private readonly AdminUrlGeneratorInterface $adminUrlGenerator,
        ) {
        }

        public function resolve(Request $request, ArgumentMetadata $argument): iterable
        {
            if (BatchActionDto::class !== $argument->getType()) {
                return [];
            }

            if (null === $context = $this->adminContextProvider->getContext()) {
                throw new \RuntimeException(sprintf('Some of your controller actions have type-hinted an argument with the "%s" class but that\'s only available for actions run to serve EasyAdmin requests. Remove the type-hint or make sure the action is part of an EasyAdmin request.', BatchActionDto::class));
            }

            yield new BatchActionDto(
                $context->getRequest()->request->get(EA::BATCH_ACTION_NAME),
                $context->getRequest()->request->all()[EA::BATCH_ACTION_ENTITY_IDS] ?? [],
                $context->getRequest()->request->get(EA::ENTITY_FQCN),
                $this->getReferrerUrl($context, $request),
                $context->getRequest()->request->get(EA::BATCH_ACTION_CSRF_TOKEN),
                false
            );
        }

        private function getReferrerUrl(AdminContext $adminContext, Request $request): string
        {
            $crudControllerFqcn = null;
            if ($adminContext->usePrettyUrls()) {
                $crudControllerFqcn = $request->attributes->get(EA::CRUD_CONTROLLER_FQCN);
            } else {
                $batchActionUrl = $adminContext->getRequest()->request->get(EA::BATCH_ACTION_URL);
                $batchActionUrlQueryString = parse_url($batchActionUrl, \PHP_URL_QUERY);
                parse_str($batchActionUrlQueryString, $batchActionUrlParts);
                $batchActionUrlParts = $request->query->all();
                $crudControllerFqcn = $batchActionUrlParts[EA::CRUD_CONTROLLER_FQCN] ?? null;
            }

            return $this->adminUrlGenerator
                // reset the page number to avoid confusing elements after the page reload
                // (we're deleting items, so the original listing pages will change)
                ->unset(EA::PAGE)
                ->setController($crudControllerFqcn)
                ->setAction(Action::INDEX)
                ->generateUrl();
        }
    }
} else {
    final class BatchActionDtoResolver implements ArgumentValueResolverInterface
    {
        private AdminContextProvider $adminContextProvider;
        private AdminUrlGeneratorInterface $adminUrlGenerator;

        public function __construct(AdminContextProviderInterface $adminContextProvider, AdminUrlGeneratorInterface $adminUrlGenerator)
        {
            $this->adminContextProvider = $adminContextProvider;
            $this->adminUrlGenerator = $adminUrlGenerator;
        }

        public function supports(Request $request, ArgumentMetadata $argument): bool
        {
            return BatchActionDto::class === $argument->getType();
        }

        public function resolve(Request $request, ArgumentMetadata $argument): iterable
        {
            if (null === $context = $this->adminContextProvider->getContext()) {
                throw new \RuntimeException(sprintf('Some of your controller actions have type-hinted an argument with the "%s" class but that\'s only available for actions run to serve EasyAdmin requests. Remove the type-hint or make sure the action is part of an EasyAdmin request.', BatchActionDto::class));
            }

            yield new BatchActionDto(
                $context->getRequest()->request->get(EA::BATCH_ACTION_NAME),
                $context->getRequest()->request->all()[EA::BATCH_ACTION_ENTITY_IDS] ?? [],
                $context->getRequest()->request->get(EA::ENTITY_FQCN),
                $this->getReferrerUrl($context, $request),
                $context->getRequest()->request->get(EA::BATCH_ACTION_CSRF_TOKEN),
                false
            );
        }

        private function getReferrerUrl(AdminContext $adminContext, Request $request): string
        {
            $crudControllerFqcn = null;
            if ($adminContext->usePrettyUrls()) {
                $crudControllerFqcn = $request->attributes->get(EA::CRUD_CONTROLLER_FQCN);
            } else {
                $batchActionUrl = $adminContext->getRequest()->request->get(EA::BATCH_ACTION_URL);
                $batchActionUrlQueryString = parse_url($batchActionUrl, \PHP_URL_QUERY);
                parse_str($batchActionUrlQueryString, $batchActionUrlParts);
                $batchActionUrlParts = $request->query->all();
                $crudControllerFqcn = $batchActionUrlParts[EA::CRUD_CONTROLLER_FQCN] ?? null;
            }

            return $this->adminUrlGenerator
                // reset the page number to avoid confusing elements after the page reload
                // (we're deleting items, so the original listing pages will change)
                ->unset(EA::PAGE)
                ->setController($crudControllerFqcn)
                ->setAction(Action::INDEX)
                ->generateUrl();
        }
    }
}

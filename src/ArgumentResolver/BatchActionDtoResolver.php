<?php

namespace EasyCorp\Bundle\EasyAdminBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/*
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class BatchActionDtoResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
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
            $context->getRequest()->request->get(EA::BATCH_ACTION_CSRF_TOKEN)
        );
    }
}

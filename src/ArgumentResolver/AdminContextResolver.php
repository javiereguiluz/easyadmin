<?php

namespace EasyCorp\Bundle\EasyAdminBundle\ArgumentResolver;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/*
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class AdminContextResolver implements ValueResolverInterface
{
    private AdminContextProvider $adminContextProvider;

    public function __construct(AdminContextProviderInterface $adminContextProvider)
    {
        $this->adminContextProvider = $adminContextProvider;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (AdminContext::class !== $argument->getType()) {
            return [];
        }

        yield $this->adminContextProvider->getContext();
    }
}

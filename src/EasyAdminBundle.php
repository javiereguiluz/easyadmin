<?php

namespace EasyCorp\Bundle\EasyAdminBundle;

use EasyCorp\Bundle\EasyAdminBundle\DependencyInjection\CreateControllerRegistriesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class EasyAdminBundle extends Bundle
{
    public const VERSION = '4.13.5-DEV';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CreateControllerRegistriesPass());
    }
}

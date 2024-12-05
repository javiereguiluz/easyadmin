<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\DataFixtures\AppFixtures;

return static function (ContainerConfigurator $container) {
    $container->parameters()->set('locale', 'en');

    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('EasyCorp\\Bundle\\EasyAdminBundle\\Tests\\PrettyUrlsTestApplication\\', '../src/*')
        ->exclude('../{Entity,Tests,Kernel.php}');

    $services->load('EasyCorp\\Bundle\\EasyAdminBundle\\Tests\\PrettyUrlsTestApplication\\Controller\\', '../src/Controller/')
        ->tag('controller.service_arguments');

    $services->set(AppFixtures::class)->tag('doctrine.fixture.orm');
};

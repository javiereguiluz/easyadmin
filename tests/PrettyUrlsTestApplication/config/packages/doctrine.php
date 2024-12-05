<?php

$config = [
    'dbal' => [
        'driver' => 'pdo_sqlite',
        'path' => '%kernel.cache_dir%/test_database.sqlite',
    ],

    'orm' => [
        'auto_generate_proxy_classes' => true,
        'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
        'auto_mapping' => true,
        'mappings' => [
            'TestEntities' => [
                'is_bundle' => false,
                'type' => 'attribute',
                'dir' => '%kernel.project_dir%/src/Entity',
                'prefix' => 'EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity',
                'alias' => 'app',
            ],
        ],
    ],
];

// TODO: make this config option unconditional when rising the Symfony requirements to 6.4
// this option was added in doctrine-bundle PR 1554, released as Doctrine Bundle 2.7.1 (https://github.com/doctrine/DoctrineBundle/releases/tag/2.7.1)
if (class_exists(Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\ControllerResolverPass::class)) {
    $config['orm']['controller_resolver'] = [
        'auto_mapping' => false,
    ];
}

$container->loadFromExtension('doctrine', $config);

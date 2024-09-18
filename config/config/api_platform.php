<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $configurator): void {
    $configurator->extension('api_platform', [
        'mapping' => [
            'paths' => [
                dirname(__DIR__) . '/api_resources',
            ],
        ],
    ]);
};

<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_ui', [
        'events' => [
            'sylius.shop.checkout.complete.summary' => [
                'blocks' => [
                    'my_block_name' => [
                        'template' => '@CommerceWeaversSyliusTpayPlugin/blik.html.twig',
                        'priority' => 5,
                        'context' => [
                            'message' => 'Hello!',
                        ],
                    ],
                ],
            ],
        ],
    ]);
};

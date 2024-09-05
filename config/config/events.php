<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_ui', [
        'events' => [
            'sylius.shop.checkout.complete.summary' => [
                'blocks' => [
                    'blik' => [
                        'template' => '@CommerceWeaversSyliusTpayPlugin/blik.html.twig',
                        'priority' => 5,
                    ],
                ],
            ],
        ],
    ]);
};

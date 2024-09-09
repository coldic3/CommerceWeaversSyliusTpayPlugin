<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_ui', [
        'events' => [
            'sylius.shop.checkout.complete.summary' => [
                'blocks' => [
                    'card' => [
                        'template' => '@CommerceWeaversSyliusTpayPlugin/shop/cart/complete/_card.html.twig',
                        'priority' => 5,
                    ],
                ],
            ],
        ],
    ]);
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\SyliusFixturesConfig;

return function(SyliusFixturesConfig $fixtures): void {
    $fixtures->suites('default')
        ->fixtures('payment_method', [
            'options' => [
                'custom' => [
                    'tpay' => [
                        'code' => 'tpay',
                        'name' => 'Tpay',
                        'gatewayFactory' => 'tpay',
                        'gatewayName' => 'Tpay',
                        'gatewayConfig' => [
                            'client_id' => '%env(string:TPAY_CLIENT_ID)%',
                            'client_secret' => '%env(string:TPAY_CLIENT_SECRET)%',
                            'production_mode' => false,
                        ],
                        'channels' => [
                            'FASHION_WEB',
                        ],
                        'enabled' => true,
                    ],
                ],
            ],
        ])
    ;
};

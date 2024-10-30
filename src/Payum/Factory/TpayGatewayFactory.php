<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\TpayApi;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class TpayGatewayFactory extends GatewayFactory
{
    public const NAME = 'tpay';

    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::NAME,
            'payum.factory_title' => self::NAME,
        ]);

        $config['payum.api'] = function (ArrayObject $config): TpayApi {
            /** @var array{client_id?: string, client_secret?: string, production_mode?: bool, notification_security_code?: string} $config */
            $clientId = $config['client_id'] ?? '';
            $clientSecret = $config['client_secret'] ?? '';
            $productionMode = $config['production_mode'] ?? false;
            $notificationSecretCode = $config['notification_security_code'] ?? null;

            return new TpayApi($clientId, $clientSecret, $productionMode, notificationSecretCode: $notificationSecretCode);
        };
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Test\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\TpayApi;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class TestTpayGatewayFactory extends GatewayFactory
{
    public const NAME = 'tpay';

    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::NAME,
            'payum.factory_title' => ucfirst(self::NAME),
        ]);

        $config['payum.api'] = function (ArrayObject $config): TpayApi {
            /** @var array{client_id?: string, client_secret?: string, production_mode?: bool} $config */
            $clientId = $config['client_id'] ?? null;
            $clientSecret = $config['client_secret'] ?? null;
            $productionMode = $config['production_mode'] ?? false;
            /** @var string $testApiUrl */
            $testApiUrl = getenv('TPAY_API_URL') !== false ? getenv('TPAY_API_URL') : null;

            if (null === $clientId || null === $clientSecret) {
                throw new \InvalidArgumentException('Tpay ClientId and ClientSecret are required.');
            }

            return new TpayApi($clientId, $clientSecret, $productionMode, apiUrlOverride: $testApiUrl);
        };
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\ValueObject\TpayClientCredentials;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

class TpayGatewayFactory extends GatewayFactory
{
    public const NAME = 'tpay';

    public function __construct (
        private TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::NAME,
            'payum.factory_title' => $this->translator->trans('commerce_weavers_sylius_tpay.admin.name'),
        ]);

        $config['payum.api'] = function (ArrayObject $config): TpayClientCredentials {
            /** @var array{clientId?: string, clientSecret?: string} $config */
            $clientId = $config['clientId'] ?? null;
            $clientSecret = $config['clientSecret'] ?? null;

            if (null === $clientId || null === $clientSecret) {
                throw new \InvalidArgumentException('Tpay ClientId and ClientSecret are required.');
            }

            return new TpayClientCredentials($clientId, $clientSecret);
        };
    }
}

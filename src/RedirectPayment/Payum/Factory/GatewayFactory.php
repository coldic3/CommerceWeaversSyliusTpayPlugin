<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\RedirectPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

final class GatewayFactory extends TpayGatewayFactory
{
    public const NAME = 'tpay_redirect';

    public function getName(): string
    {
        return self::NAME;
    }
}

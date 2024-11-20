<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\BlikPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

final class GatewayFactory extends TpayGatewayFactory
{
    public const NAME = 'tpay_blik';

    public function getName(): string
    {
        return self::NAME;
    }
}

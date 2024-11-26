<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

final class GatewayFactory extends TpayGatewayFactory
{
    public const NAME = 'tpay_google_pay';

    public function getName(): string
    {
        return self::NAME;
    }
}

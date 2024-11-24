<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

final class GatewayFactory extends TpayGatewayFactory
{
    public const NAME = 'tpay_pbl';

    public function getName(): string
    {
        return self::NAME;
    }
}

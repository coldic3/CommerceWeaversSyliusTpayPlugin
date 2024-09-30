<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory;

use Tpay\OpenApi\Utilities\phpseclib\File\X509;

final class X509Factory implements X509FactoryInterface
{
    public function create(): X509
    {
        return new X509();
    }
}

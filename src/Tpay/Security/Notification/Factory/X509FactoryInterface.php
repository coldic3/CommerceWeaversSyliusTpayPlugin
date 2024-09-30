<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory;

use Tpay\OpenApi\Utilities\phpseclib\File\X509;

interface X509FactoryInterface
{
    public function create(): X509;
}

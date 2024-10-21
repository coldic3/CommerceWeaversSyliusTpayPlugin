<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use Payum\Core\Bridge\Spl\ArrayObject;

interface CreateInitializeApplePayPaymentPayloadFactoryInterface
{
    public function create(ArrayObject $data): array;
}

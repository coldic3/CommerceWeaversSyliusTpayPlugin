<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use Payum\Core\Bridge\Spl\ArrayObject;

final class CreateInitializeApplePayPaymentPayloadFactory implements CreateInitializeApplePayPaymentPayloadFactoryInterface
{
    public function create(ArrayObject $data): array
    {
        return [
            'domainName' => $data['domainName'],
            'displayName' => $data['displayName'],
            'validationUrl' => $data['validationUrl'],
        ];
    }
}

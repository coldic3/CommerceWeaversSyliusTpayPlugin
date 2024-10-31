<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;

interface PayWithCardActionPayloadMapperInterface
{
    /**
     * @return array{'groupId': int, 'cardPaymentData': array}
     */
    public function getPayload(PaymentDetails $paymentDetails): array;
}

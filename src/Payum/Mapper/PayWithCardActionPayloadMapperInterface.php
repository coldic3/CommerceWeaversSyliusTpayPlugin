<?php

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;

interface PayWithCardActionPayloadMapperInterface
{
    /**
     * @param PaymentDetails $paymentDetails
     *
     * @return array<'groupId' => string, 'cardPaymentData' => array>
     */
    public function getPayload(PaymentDetails $paymentDetails): array;
}

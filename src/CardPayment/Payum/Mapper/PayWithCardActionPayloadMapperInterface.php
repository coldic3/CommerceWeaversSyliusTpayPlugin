<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;

interface PayWithCardActionPayloadMapperInterface
{
    /**
     * @return array{'groupId': int, 'cardPaymentData': array}
     */
    public function getPayload(PaymentDetails $paymentDetails, PaymentInterface $payment): array;
}

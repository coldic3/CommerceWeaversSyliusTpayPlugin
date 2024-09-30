<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier;

use Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment;

interface ChecksumVerifierInterface
{
    public function verify(BasicPayment $paymentData, string $merchantSecret): bool;
}

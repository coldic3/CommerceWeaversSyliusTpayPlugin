<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier;

use Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment;
use Tpay\OpenApi\Utilities\Util;

final class ChecksumVerifier implements ChecksumVerifierInterface
{
    public function verify(BasicPayment $paymentData, string $merchantSecret): bool
    {
        $expectedChecksum = $paymentData->md5sum;
        $calculatedChecksum = md5(
            implode(
                '',
                [
                    $paymentData->id,
                    $paymentData->tr_id,
                    Util::numberFormat($paymentData->tr_amount),
                    $paymentData->tr_crc,
                    $merchantSecret,
                ],
            ),
        );

        return $expectedChecksum === $calculatedChecksum;
    }
}

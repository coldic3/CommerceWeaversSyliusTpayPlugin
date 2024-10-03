<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier;

use Tpay\OpenApi\Model\Fields\Field;
use Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment;
use Tpay\OpenApi\Utilities\Util;

final class ChecksumVerifier implements ChecksumVerifierInterface
{
    public function verify(BasicPayment $paymentData, string $merchantSecret): bool
    {
        /** @var float|int $amount */
        $amount = $paymentData->tr_amount instanceof Field ? $paymentData->tr_amount->getValue() : $paymentData->tr_amount;

        /** @var string $expectedChecksum */
        $expectedChecksum = $paymentData->md5sum instanceof Field ? $paymentData->md5sum->getValue() : $paymentData->md5sum;

        $calculatedChecksum = md5(
            implode(
                '',
                [
                    $paymentData->id instanceof Field ? $paymentData->id->getValue() : $paymentData->id,
                    $paymentData->tr_id instanceof Field ? $paymentData->tr_id->getValue() : $paymentData->tr_id,
                    Util::numberFormat($amount),
                    $paymentData->tr_crc instanceof Field ? $paymentData->tr_crc->getValue() : $paymentData->tr_crc,
                    $merchantSecret,
                ],
            ),
        );

        return $expectedChecksum === $calculatedChecksum;
    }
}

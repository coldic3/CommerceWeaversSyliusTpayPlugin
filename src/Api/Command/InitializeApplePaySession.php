<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract\OrderTokenAwareInterface;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract\PaymentIdAwareInterface;

final class InitializeApplePaySession implements OrderTokenAwareInterface, PaymentIdAwareInterface
{
    public function __construct (
        public readonly string $orderToken,
        public readonly int $paymentId,
        public readonly string $domainName,
        public readonly string $displayName,
        public readonly string $validationUrl,
    ) {
    }

    public static function getOrderTokenPropertyName(): string
    {
        return 'orderToken';
    }

    public static function getPaymentIdPropertyName(): string
    {
        return 'paymentId';
    }
}

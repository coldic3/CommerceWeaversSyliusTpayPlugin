<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Checker;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPluginAvailabilityChecker implements RefundPluginAvailabilityCheckerInterface
{
    public function isAvailable(): bool
    {
        return interface_exists(RefundPaymentInterface::class);
    }
}

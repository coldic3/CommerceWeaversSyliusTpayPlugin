<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payment\Checker;

use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentCancellationPossibilityCheckerInterface
{
    public function canBeCancelled(PaymentInterface $payment): bool;
}

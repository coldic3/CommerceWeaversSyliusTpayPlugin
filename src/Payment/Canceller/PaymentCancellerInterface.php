<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payment\Canceller;

use CommerceWeavers\SyliusTpayPlugin\Payment\Exception\PaymentCannotBeCancelledException;
use Sylius\Component\Core\Model\PaymentInterface;

interface PaymentCancellerInterface
{
    /**
     * @throws PaymentCannotBeCancelledException
     */
    public function cancel(PaymentInterface $payment): void;
}

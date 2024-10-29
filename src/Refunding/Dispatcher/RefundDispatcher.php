<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher;

use CommerceWeavers\SyliusTpayPlugin\Refunding\Checker\RefundPluginAvailabilityCheckerInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Refund;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundDispatcher implements RefundDispatcherInterface
{
    public function __construct(
        private readonly GatewayInterface $gateway,
        private readonly RefundPluginAvailabilityCheckerInterface $refundPluginAvailabilityChecker,
    ) {
    }

    public function dispatch(PaymentInterface|RefundPaymentInterface $payment): void
    {
        if (!$this->checkIfShouldBeDispatched($payment)) {
            return;
        }

        $this->gateway->execute(new Refund($payment));
    }

    private function checkIfShouldBeDispatched(PaymentInterface|RefundPaymentInterface $payment): bool
    {
        $isRefundPaymentAvailable = $this->refundPluginAvailabilityChecker->isAvailable();
        $isPayment = $payment instanceof PaymentInterface;
        $isRefundPayment = $payment instanceof RefundPaymentInterface;

        return (!$isRefundPaymentAvailable && $isPayment) || ($isRefundPaymentAvailable && $isRefundPayment);
    }
}

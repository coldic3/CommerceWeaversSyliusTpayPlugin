<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher;

use Payum\Core\Payum;
use Payum\Core\Request\Refund;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class RefundDispatcher implements RefundDispatcherInterface
{
    public function __construct (
        private Payum $payum,
    ) {
    }

    public function dispatch(PaymentInterface $payment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        $this->payum->getGateway($gatewayConfig->getGatewayName())->execute(new Refund($payment));
    }
}

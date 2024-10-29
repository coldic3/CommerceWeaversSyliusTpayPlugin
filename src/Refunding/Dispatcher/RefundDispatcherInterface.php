<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundDispatcherInterface
{
    public function dispatch(PaymentInterface|RefundPaymentInterface $payment): void;
}

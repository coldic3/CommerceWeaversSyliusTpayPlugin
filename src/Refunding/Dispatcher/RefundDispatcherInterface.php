<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher;

use Sylius\Component\Core\Model\PaymentInterface;

interface RefundDispatcherInterface
{
    public function dispatch(PaymentInterface $payment): void;
}

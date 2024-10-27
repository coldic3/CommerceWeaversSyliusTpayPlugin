<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Processor;

use Sylius\Component\Core\Model\PaymentInterface;

interface CreateTransactionProcessorInterface
{
    public function process(PaymentInterface $payment): void;
}

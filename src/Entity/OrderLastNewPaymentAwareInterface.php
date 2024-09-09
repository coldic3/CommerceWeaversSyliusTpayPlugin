<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;

interface OrderLastNewPaymentAwareInterface
{
    public function getLastNewPayment(): ?PaymentInterface;
}

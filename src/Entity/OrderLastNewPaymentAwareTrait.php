<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Entity;

use Sylius\Component\Core\Model\PaymentInterface;

trait OrderLastNewPaymentAwareTrait
{
    public function getLastNewPayment(): ?PaymentInterface
    {
        return $this->getLastPayment('cart');
    }
}

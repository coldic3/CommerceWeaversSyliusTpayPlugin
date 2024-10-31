<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Factory;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use Sylius\Resource\Factory\FactoryInterface;

interface CreditCardFactoryInterface extends FactoryInterface
{
    public function createNew(): CreditCardInterface;
}

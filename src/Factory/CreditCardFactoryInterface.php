<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Factory;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Sylius\Resource\Factory\FactoryInterface;

interface CreditCardFactoryInterface extends FactoryInterface
{
    public function createNew(): CreditCardInterface;
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Factory;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Factory\FactoryInterface;

interface BlikAliasFactoryInterface extends FactoryInterface
{
    public function createForCustomerAndChannel(CustomerInterface $customer, ChannelInterface $channel): BlikAliasInterface;
}

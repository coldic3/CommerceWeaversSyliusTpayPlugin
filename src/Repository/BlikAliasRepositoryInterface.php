<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

interface BlikAliasRepositoryInterface extends RepositoryInterface
{
    public function findOneByValue(string $value): ?BlikAliasInterface;

    public function findOneByCustomerAndChannel(
        CustomerInterface $customer,
        ChannelInterface $channel,
    ): ?BlikAliasInterface;
}

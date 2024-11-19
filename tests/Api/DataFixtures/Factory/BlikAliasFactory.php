<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api\DataFixtures\Factory;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAlias;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasFactory
{
    public static function create(CustomerInterface $customer, ChannelInterface $channel, string $value): BlikAlias
    {
        $blikAlias = new BlikAlias();

        $blikAlias->setCustomer($customer);
        $blikAlias->setChannel($channel);
        $blikAlias->setValue($value);

        return $blikAlias;
    }

    public static function createRegistered(
        CustomerInterface $customer,
        ChannelInterface $channel,
        string $value,
        \DateTimeInterface $expirationDate
    ): BlikAlias {
        $blikAlias = self::create($customer, $channel, $value);

        $blikAlias->register($expirationDate);

        return $blikAlias;
    }
}

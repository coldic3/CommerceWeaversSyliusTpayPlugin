<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api\DataFixtures\Factory;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAlias;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasFactory
{
    public static function createRegistered(CustomerInterface $customer, string $value, \DateTimeInterface $expirationDate): BlikAlias
    {
        $blikAlias = new BlikAlias();

        $blikAlias->setCustomer($customer);
        $blikAlias->setValue($value);
        $blikAlias->register($expirationDate);

        return $blikAlias;
    }
}

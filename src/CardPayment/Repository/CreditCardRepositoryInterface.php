<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Repository;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CreditCardRepositoryInterface extends RepositoryInterface
{
    public function createByCustomerListQueryBuilder(?CustomerInterface $customer, ?ChannelInterface $channel): QueryBuilder;

    public function findOneByIdCustomerAndChannel(mixed $id, ?CustomerInterface $customer, ?ChannelInterface $channel): ?CreditCardInterface;

    public function findByCustomerAndChannel(?CustomerInterface $customer, ?ChannelInterface $channel): array;

    public function hasCustomerAnyCreditCardInGivenChannel(?CustomerInterface $customer, ?ChannelInterface $channel): bool;
}

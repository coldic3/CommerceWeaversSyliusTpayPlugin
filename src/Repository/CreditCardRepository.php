<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CreditCardRepository extends EntityRepository implements CreditCardRepositoryInterface
{
    public function createByCustomerListQueryBuilder(CustomerInterface $customer, ChannelInterface $channel): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.channel = :channel')
            ->setParameter('customer', $customer)
            ->setParameter('channel', $channel)
        ;
    }

    public function findOneByChannelAndCustomer(mixed $customerId, mixed $channelId): ?CreditCardInterface
    {
        /** @phpstan-var CreditCardInterface|null */
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.channel = :channel')
            ->setParameter('customer', $customerId)
            ->setParameter('channel', $channelId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

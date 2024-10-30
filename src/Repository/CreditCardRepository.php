<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CreditCardRepository extends EntityRepository implements CreditCardRepositoryInterface
{
    public function createByCustomerListQueryBuilder(?CustomerInterface $customer, ?ChannelInterface $channel): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.channel = :channel')
            ->setParameter('customer', $customer)
            ->setParameter('channel', $channel)
        ;
    }

    public function findOneByIdCustomerAndChannel(mixed $id, ?CustomerInterface $customer, ?ChannelInterface $channel): ?CreditCardInterface
    {
        /** @phpstan-var CreditCardInterface|null */
        return $this->createByCustomerListQueryBuilder($customer, $channel)
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByCustomerAndChannel(?CustomerInterface $customer, ?ChannelInterface $channel): array
    {
        return $this->createByCustomerListQueryBuilder($customer, $channel)
            ->getQuery()
            ->getResult()
        ;
    }

    public function hasCustomerAnyCreditCardInGivenChannel(?CustomerInterface $customer, ?ChannelInterface $channel): bool
    {
        return 0 !== $this->createByCustomerListQueryBuilder($customer, $channel)
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

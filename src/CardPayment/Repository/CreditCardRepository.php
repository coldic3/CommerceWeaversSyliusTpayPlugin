<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Repository;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

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
        $result = $this->createByCustomerListQueryBuilder($customer, $channel)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($result);

        return $result;
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

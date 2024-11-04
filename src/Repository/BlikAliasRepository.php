<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasRepository extends EntityRepository implements BlikAliasRepositoryInterface
{
    public function findOneByValue(string $value): ?BlikAliasInterface
    {
        /** @phpstan-var BlikAliasInterface|null */
        return $this->createQueryBuilder('o')
            ->andWhere('o.value = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCustomerAndChannel(
        CustomerInterface $customer,
        ChannelInterface $channel,
    ): ?BlikAliasInterface {
        /** @phpstan-var BlikAliasInterface|null */
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.channel = :channel')
            ->setParameter('customer', $customer)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

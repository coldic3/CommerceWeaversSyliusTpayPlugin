<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class CreditCardRepository extends EntityRepository
{
    public function createByCustomerListQueryBuilder(mixed $customerId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customerId)
        ;
    }

    public function findOneByCustomer(mixed $customerId): ?CreditCardInterface
    {
        /** @phpstan-var CreditCardInterface|null */
        return $this->createByCustomerListQueryBuilder($customerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

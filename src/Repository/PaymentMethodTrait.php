<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use CommerceWeavers\SyliusTpayPlugin\Tpay\GatewayName;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @mixin EntityRepository
 */
trait PaymentMethodTrait
{
    public function findByChannelAndGatewayConfigNameWithGatewayConfig(ChannelInterface $channel, array $gatewayConfigNames): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('gatewayConfig')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig', Join::WITH, 'gatewayConfig.gatewayName IN (:gatewayNames)')
            ->where('o.enabled = :enabled')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('enabled', true)
            ->setParameter('channel', $channel)
            ->setParameter('gatewayNames', GatewayName::all())
            ->getQuery()
            ->getResult()
        ;
    }
}

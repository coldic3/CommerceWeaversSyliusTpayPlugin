<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @mixin EntityRepository
 */
trait FindByChannelWithGatewayConfigTrait
{
    public function findByChannelAndGatewayConfigNameWithGatewayConfig(ChannelInterface $channel, string $gatewayConfigName): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('gatewayConfig')
            ->innerJoin('o.gatewayConfig', 'gatewayConfig', Join::WITH, 'gatewayConfig.gatewayName = :gatewayName')
            ->where('o.enabled = :enabled')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('enabled', true)
            ->setParameter('channel', $channel)
            ->setParameter('gatewayName', $gatewayConfigName)
            ->getQuery()
            ->getResult()
        ;
    }
}

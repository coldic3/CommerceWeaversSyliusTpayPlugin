<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use Doctrine\ORM\EntityRepository;
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
            ->leftJoin('o.gatewayConfig', 'gatewayConfig')
            ->where('o.enabled = :enabled')
            ->andWhere(':channel MEMBER OF pm.channels')
            ->andWhere('gatewayConfig.gatewayName = :gatewayName')
            ->setParameter('enabled', true)
            ->setParameter('channel', $channel)
            ->setParameter('gatewayName', $gatewayConfigName)
            ->getQuery()
            ->getResult()
        ;
    }
}

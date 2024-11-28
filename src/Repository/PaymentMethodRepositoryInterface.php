<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{
    /**
     * @param array<string> $gatewayConfigNames
     *
     * @return array<PaymentMethodInterface>
     */
    public function findByChannelAndGatewayConfigNameWithGatewayConfig(ChannelInterface $channel, array $gatewayConfigNames): array;
}

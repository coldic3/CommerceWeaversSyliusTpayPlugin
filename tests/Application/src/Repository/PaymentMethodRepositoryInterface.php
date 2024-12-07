<?php

namespace App\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

interface PaymentMethodRepositoryInterface extends BasePaymentMethodRepositoryInterface
{
    public function findByChannelAndGatewayConfigNameWithGatewayConfig(ChannelInterface $channel, string $gatewayConfigName): array;
}

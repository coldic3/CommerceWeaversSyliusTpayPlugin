<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface as BasePaymentMethodRepositoryInterface;

interface FindByChannelWithGatewayConfigTraitInterface extends BasePaymentMethodRepositoryInterface
{
    public function findByChannelWithGatewayConfig(ChannelInterface $channel): array;
}

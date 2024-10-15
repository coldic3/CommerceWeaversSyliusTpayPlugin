<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use CommerceWeavers\SyliusTpayPlugin\Api\Resource\TpayChannel;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;

final class TpayChannelItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $tpayTransactionChannelResolver,
    ) {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?TpayChannel
    {
        $transactionChannels = $this->tpayTransactionChannelResolver->resolve();

        if (array_key_exists($id, $transactionChannels)) {
            return TpayChannel::fromArray($transactionChannels[$id]);
        }

        return null;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return TpayChannel::class === $resourceClass;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use CommerceWeavers\SyliusTpayPlugin\Api\Resource\TpayChannel;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;

final class TpayChannelCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $tpayTransactionChannelResolver,
    ) {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $transactionChannels = $this->tpayTransactionChannelResolver->resolve();

        foreach ($transactionChannels as $transactionChannel) {
            yield TpayChannel::fromArray($transactionChannel);
        }
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return TpayChannel::class === $resourceClass;
    }
}

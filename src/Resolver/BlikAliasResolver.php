<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Resolver;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\Factory\BlikAliasFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Repository\BlikAliasRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasResolver implements BlikAliasResolverInterface
{
    public function __construct(
        private readonly BlikAliasRepositoryInterface $blikAliasRepository,
        private readonly BlikAliasFactoryInterface $blikAliasFactory,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    public function resolve(CustomerInterface $customer): BlikAliasInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->blikAliasRepository->findOneByCustomerAndChannel($customer, $channel)
            ?? $this->blikAliasFactory->createForCustomerAndChannel($customer, $channel);
    }
}

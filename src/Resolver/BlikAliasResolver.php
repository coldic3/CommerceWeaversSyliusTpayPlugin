<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Resolver;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Factory\BlikAliasFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Repository\BlikAliasRepositoryInterface;
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

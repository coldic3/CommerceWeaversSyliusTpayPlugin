<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Resolver;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\Factory\BlikAliasFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Repository\BlikAliasRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Resolver\BlikAliasResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasResolverTest extends TestCase
{
    use ProphecyTrait;

    private BlikAliasRepositoryInterface|ObjectProphecy $blikAliasRepository;

    private BlikAliasFactoryInterface|ObjectProphecy $blikAliasFactory;

    private ChannelContextInterface|ObjectProphecy $channelContext;

    private CustomerInterface|ObjectProphecy $customer;

    protected function setUp(): void
    {
        $this->blikAliasRepository = $this->prophesize(BlikAliasRepositoryInterface::class);
        $this->blikAliasFactory = $this->prophesize(BlikAliasFactoryInterface::class);
        $this->channelContext = $this->prophesize(ChannelContextInterface::class);
        $this->customer = $this->prophesize(CustomerInterface::class);
    }

    public function test_it_resolves_blik_alias_from_repository(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class)->reveal();
        $this->channelContext->getChannel()->willReturn($channel);
        $this->blikAliasRepository->findOneByCustomerAndChannel($this->customer, $channel)->willReturn($blikAlias);

        $result = (new BlikAliasResolver(
            $this->blikAliasRepository->reveal(),
            $this->blikAliasFactory->reveal(),
            $this->channelContext->reveal(),
        ))->resolve($this->customer->reveal());

        $this->assertSame($blikAlias, $result);
    }

    public function test_it_resolves_blik_alias_from_factory(): void
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class)->reveal();
        $this->channelContext->getChannel()->willReturn($channel);
        $this->blikAliasRepository->findOneByCustomerAndChannel($this->customer, $channel)->willReturn(null);
        $this->blikAliasFactory->createForCustomerAndChannel($this->customer, $channel)->willReturn($blikAlias);

        $result = (new BlikAliasResolver(
            $this->blikAliasRepository->reveal(),
            $this->blikAliasFactory->reveal(),
            $this->channelContext->reveal(),
        ))->resolve($this->customer->reveal());

        $this->assertSame($blikAlias, $result);
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\PreconditionGuard;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\ActiveBlikAliasPreconditionGuard;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasExpiredException;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasNotRegisteredException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Calendar\Provider\DateTimeProviderInterface;

final class ActiveBlikAliasPreconditionGuardTest extends TestCase
{
    use ProphecyTrait;

    private DateTimeProviderInterface|ObjectProphecy $dateTimeProvider;

    protected function setUp(): void
    {
        $this->dateTimeProvider = $this->prophesize(DateTimeProviderInterface::class);
    }

    public function test_it_throws_exception_if_blik_alias_is_not_registered(): void
    {
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->isRegistered()->willReturn(false);

        $this->expectException(BlikAliasNotRegisteredException::class);

        $this->createTestSubject()->denyIfNotActive($blikAlias->reveal());
    }

    public function test_it_throws_exception_if_blik_alias_is_expired(): void
    {
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->isRegistered()->willReturn(true);
        $blikAlias->getExpirationDate()->willReturn(new \DateTimeImmutable('yesterday'));
        $this->dateTimeProvider->now()->willReturn(new \DateTimeImmutable('today'));

        $this->expectException(BlikAliasExpiredException::class);

        $this->createTestSubject()->denyIfNotActive($blikAlias->reveal());
    }

    public function test_it_does_nothing_if_blik_alias_is_registered_and_not_expired(): void
    {
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->isRegistered()->willReturn(true);
        $blikAlias->getExpirationDate()->willReturn(new \DateTimeImmutable('tomorrow'));
        $this->dateTimeProvider->now()->willReturn(new \DateTimeImmutable('today'));

        $this->expectNotToPerformAssertions();

        $this->createTestSubject()->denyIfNotActive($blikAlias->reveal());
    }

    private function createTestSubject(): ActiveBlikAliasPreconditionGuard
    {
        return new ActiveBlikAliasPreconditionGuard($this->dateTimeProvider->reveal());
    }
}

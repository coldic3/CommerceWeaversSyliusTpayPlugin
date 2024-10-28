<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PreconditionGuard;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasExpiredException;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasNotRegisteredException;
use Psr\Clock\ClockInterface;

final class ActiveBlikAliasPreconditionGuard implements ActiveBlikAliasPreconditionGuardInterface
{
    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function denyIfNotActive(BlikAliasInterface $blikAlias): void
    {
        if (!$blikAlias->isRegistered()) {
            throw new BlikAliasNotRegisteredException();
        }

        if ($blikAlias->getExpirationDate() < $this->clock->now()) {
            throw new BlikAliasExpiredException();
        }
    }
}

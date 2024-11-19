<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PreconditionGuard;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasExpiredException;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasNotRegisteredException;
use Sylius\Calendar\Provider\DateTimeProviderInterface;

final class ActiveBlikAliasPreconditionGuard implements ActiveBlikAliasPreconditionGuardInterface
{
    public function __construct(private readonly DateTimeProviderInterface $dateTimeProvider)
    {
    }

    public function denyIfNotActive(BlikAliasInterface $blikAlias): void
    {
        if (!$blikAlias->isRegistered()) {
            throw new BlikAliasNotRegisteredException();
        }

        if ($blikAlias->getExpirationDate() < $this->dateTimeProvider->now()) {
            throw new BlikAliasExpiredException();
        }
    }
}

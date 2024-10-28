<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PreconditionGuard;

use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasExpiredException;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception\BlikAliasNotRegisteredException;

interface ActiveBlikAliasPreconditionGuardInterface
{
    /**
     * @throws BlikAliasExpiredException
     * @throws BlikAliasNotRegisteredException
     */
    public function denyIfNotActive(BlikAliasInterface $blikAlias): void;
}

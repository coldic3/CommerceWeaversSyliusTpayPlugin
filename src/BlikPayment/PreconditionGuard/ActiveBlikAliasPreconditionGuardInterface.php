<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\BlikPayment\PreconditionGuard;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\PreconditionGuard\Exception\BlikAliasExpiredException;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\PreconditionGuard\Exception\BlikAliasNotRegisteredException;

interface ActiveBlikAliasPreconditionGuardInterface
{
    /**
     * @throws BlikAliasExpiredException
     * @throws BlikAliasNotRegisteredException
     */
    public function denyIfNotActive(BlikAliasInterface $blikAlias): void;
}

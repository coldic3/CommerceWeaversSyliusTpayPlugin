<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Checker;

interface ProductionModeCheckerInterface
{
    public function isProduction(string $x5u): bool;
}

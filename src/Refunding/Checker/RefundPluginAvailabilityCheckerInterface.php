<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Refunding\Checker;

interface RefundPluginAvailabilityCheckerInterface
{
    public function isAvailable(): bool;
}

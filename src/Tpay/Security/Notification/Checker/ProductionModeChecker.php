<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Checker;

final class ProductionModeChecker implements ProductionModeCheckerInterface
{
    private const SANDBOX_HOST = 'secure.tpay.com';

    public function isProduction(string $x5u): bool
    {
        $x5uHost = parse_url($x5u, \PHP_URL_HOST);

        return self::SANDBOX_HOST === $x5uHost;
    }
}

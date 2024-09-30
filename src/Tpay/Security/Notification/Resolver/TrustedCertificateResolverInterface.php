<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver;

interface TrustedCertificateResolverInterface
{
    public function resolve(bool $production = false): string;
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver;

interface CertificateResolverInterface
{
    public function resolve(string $x5u): string;
}

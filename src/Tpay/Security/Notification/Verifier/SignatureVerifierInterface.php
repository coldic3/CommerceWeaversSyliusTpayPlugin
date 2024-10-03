<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier;

interface SignatureVerifierInterface
{
    public function verify(string $jws, string $requestContent): bool;
}

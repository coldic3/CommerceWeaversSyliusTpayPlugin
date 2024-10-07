<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver;

interface TpayTransactionChannelResolverInterface
{
    public function resolve(): array;
}

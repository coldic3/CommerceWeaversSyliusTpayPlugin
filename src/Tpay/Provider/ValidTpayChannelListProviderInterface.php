<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

interface ValidTpayChannelListProviderInterface
{
    public function provide(): array;
}

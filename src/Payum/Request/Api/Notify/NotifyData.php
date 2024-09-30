<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;

class NotifyData
{
    public function __construct(
        readonly string $jws,
        readonly string $requestContent,
        readonly array $requestParameters,
    ) {
    }
}

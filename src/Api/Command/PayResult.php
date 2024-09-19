<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

final class PayResult
{
    public function __construct(
        public readonly string $status,
    ) {
    }
}

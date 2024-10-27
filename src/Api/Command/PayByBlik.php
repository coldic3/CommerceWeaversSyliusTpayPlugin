<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Enum\BlikAliasAction;

final class PayByBlik
{
    public function __construct(
        public readonly int $paymentId,
        public readonly ?string $blikToken,
        public readonly ?BlikAliasAction $blikAliasAction,
        public readonly ?string $blikAliasApplicationCode,
    ) {
    }
}

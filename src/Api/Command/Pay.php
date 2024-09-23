<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract\OrderTokenAwareInterface;

final class Pay implements OrderTokenAwareInterface
{
    public function __construct(
        public readonly string $orderToken,
        public readonly ?string $blikToken = null,
        public readonly ?string $encodedCardData = null,
    ) {
    }

    public static function getOrderTokenPropertyName(): string
    {
        return 'orderToken';
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract\OrderTokenAwareInterface;
use CommerceWeavers\SyliusTpayPlugin\Api\Enum\BlikAliasAction;

final class Pay implements OrderTokenAwareInterface
{
    public function __construct(
        public readonly string $orderToken,
        public readonly string $successUrl,
        public readonly string $failureUrl,
        public readonly ?string $applePayToken = null,
        public readonly ?string $blikToken = null,
        public readonly ?BlikAliasAction $blikAliasAction = null,
        public readonly ?string $blikAliasApplicationCode = null,
        public readonly ?string $googlePayToken = null,
        public readonly ?string $encodedCardData = null,
        public readonly ?int $savedCardId = null,
        public readonly ?bool $saveCard = null,
        public readonly ?string $tpayChannelId = null,
        public readonly ?string $visaMobilePhoneNumber = null,
    ) {
    }

    public static function getOrderTokenPropertyName(): string
    {
        return 'orderToken';
    }
}

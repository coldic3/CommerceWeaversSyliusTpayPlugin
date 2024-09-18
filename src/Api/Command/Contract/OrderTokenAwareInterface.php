<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract;

interface OrderTokenAwareInterface
{
    public static function getOrderTokenPropertyName(): string;
}

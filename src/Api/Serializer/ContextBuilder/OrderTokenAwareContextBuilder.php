<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Serializer\ContextBuilder;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Contract\OrderTokenAwareInterface;

final class OrderTokenAwareContextBuilder extends AbstractAwareContextBuilder
{
    public function getAttributeKey(): string
    {
        return 'tokenValue';
    }

    public function getSupportedInterface(): string
    {
        return OrderTokenAwareInterface::class;
    }

    public function getPropertyNameAccessorMethodName(): string
    {
        return 'getOrderTokenPropertyName';
    }
}

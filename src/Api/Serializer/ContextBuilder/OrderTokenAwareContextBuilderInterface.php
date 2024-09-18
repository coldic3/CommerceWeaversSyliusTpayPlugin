<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Serializer\ContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

interface OrderTokenAwareContextBuilderInterface extends SerializerContextBuilderInterface
{
    public function supports(Request $request, array $context, ?array $extractedAttributes): bool;
}

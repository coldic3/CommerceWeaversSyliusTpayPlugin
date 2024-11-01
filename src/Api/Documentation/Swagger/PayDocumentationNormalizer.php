<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Documentation\Swagger;

use CommerceWeavers\SyliusTpayPlugin\Api\Documentation\PayRequestBodyExampleFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PayDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly NormalizerInterface $decoratedNormalizer,
        private readonly string $apiShopRoutePrefix,
    ) {
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $payPath = sprintf('%s/orders/{tokenValue}/pay', $this->apiShopRoutePrefix);
        if (!isset($docs['paths'][$payPath]['post']['requestBody']['content']['application/ld+json'])) {
            return $docs;
        }

        $docs['paths'][$payPath]['post']['requestBody']['content']['application/ld+json']['examples'] = PayRequestBodyExampleFactory::create();

        return $docs;
    }
}

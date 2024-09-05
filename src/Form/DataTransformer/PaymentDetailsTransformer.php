<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PaymentDetailsTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if (!$value || !array_key_exists('blik', $value)) {
            return '';
        }

        return $value['blik'];
    }
    public function reverseTransform($value): array
    {
        return $value ? ['blik' => $value] : [];
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

class PaymentDetailsTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): string
    {
        Assert::isArray($value);
        if ($value === [] || !array_key_exists('blik', $value)) {
            return '';
        }

        return $value['blik'];
    }

    public function reverseTransform($value): array
    {
        return ['blik' => $value];
    }
}

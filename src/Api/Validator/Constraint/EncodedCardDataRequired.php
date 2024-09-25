<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class EncodedCardDataRequired extends Constraint
{
    public const ENCODED_CARD_DATA_REQUIRED_ERROR = '15b746fa-1620-4325-ada6-e1c0c5e574b3';

    public string $message = 'commerce_weavers_sylius_tpay.shop.pay.encoded_card_data.required';

    /**
     * @return array<string>
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}

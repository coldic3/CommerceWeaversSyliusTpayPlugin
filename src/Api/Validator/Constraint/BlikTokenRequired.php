<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class BlikTokenRequired extends Constraint
{
    public const BLIK_TOKEN_REQUIRED_ERROR = 'efb11914-58c3-4a70-ae21-ab480d22379b';

    public string $message = 'commerce_weavers_sylius_tpay.shop.pay.blik.required';

    /**
     * @return array<string>
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use Payum\Core\Request\Generic;

class SaveCreditCard extends Generic
{
    public function __construct(
        mixed $model,
        public readonly string $cardToken,
        public readonly string $cardBrand,
        public readonly string $cardTail,
        public readonly string $tokenExpiryDate,

    ) {
        parent::__construct($model);
    }
}

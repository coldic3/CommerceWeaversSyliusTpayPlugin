<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Request\Api;

use Payum\Core\Request\Generic;

class SaveCreditCard extends Generic
{
    public function __construct(
        mixed $model,
        private readonly string $cardToken,
        private readonly string $cardBrand,
        private readonly string $cardTail,
        private readonly string $tokenExpiryDate,
    ) {
        parent::__construct($model);
    }

    public function getCardToken(): string
    {
        return $this->cardToken;
    }

    public function getCardBrand(): string
    {
        return $this->cardBrand;
    }

    public function getCardTail(): string
    {
        return $this->cardTail;
    }

    public function getTokenExpiryDate(): string
    {
        return $this->tokenExpiryDate;
    }
}

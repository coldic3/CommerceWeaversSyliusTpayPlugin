<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use Payum\Core\Request\Generic;

class FetchPaymentDetails extends Generic
{
    public function __construct (
        private string $transactionId,
        mixed $model,
    ) {
        parent::__construct($model);
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;

final class CreateTransactionFactory implements CreateTransactionFactoryInterface
{
    public function createNewWithModel(mixed $model): CreateTransaction
    {
        return new CreateTransaction($model);
    }
}

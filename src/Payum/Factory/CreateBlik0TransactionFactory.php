<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateBlik0Transaction;

final class CreateBlik0TransactionFactory implements CreateTransactionFactoryInterface
{
    public function createNewWithModel(mixed $model): CreateBlik0Transaction
    {
        return new CreateBlik0Transaction($model);
    }
}

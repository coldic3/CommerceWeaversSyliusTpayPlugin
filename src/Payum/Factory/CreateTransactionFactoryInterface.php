<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;

interface CreateTransactionFactoryInterface
{
    public function createNewWithModel(mixed $model): CreateTransaction;
}

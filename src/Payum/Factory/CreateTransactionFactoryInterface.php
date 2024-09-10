<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use Payum\Core\Request\Generic;

interface CreateTransactionFactoryInterface
{
    public function createNewWithModel(mixed $model): Generic;
}

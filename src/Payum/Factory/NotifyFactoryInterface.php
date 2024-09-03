<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use Payum\Core\Bridge\Spl\ArrayObject;

interface NotifyFactoryInterface
{
    public function createNewWithModel(mixed $model, ArrayObject $data): Notify;
}

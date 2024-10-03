<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify\NotifyData;

final class NotifyFactory implements NotifyFactoryInterface
{
    public function createNewWithModel(mixed $model, NotifyData $data): Notify
    {
        return new Notify($model, $data);
    }
}

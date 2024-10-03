<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify\NotifyData;

final class NotifyDataFactory implements NotifyDataFactoryInterface
{
    public function create(string $jws, string $requestContent, array $requestParameters): NotifyData
    {
        return new NotifyData($jws, $requestContent, $requestParameters);
    }
}

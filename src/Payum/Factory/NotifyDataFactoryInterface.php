<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify\NotifyData;

interface NotifyDataFactoryInterface
{
    public function create(
        string $jws,
        string $requestContent,
        array $requestParameters,
    ): NotifyData;
}

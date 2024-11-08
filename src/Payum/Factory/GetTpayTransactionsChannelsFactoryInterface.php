<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\GetTpayTransactionsChannels;

interface GetTpayTransactionsChannelsFactoryInterface
{
    public function createNewEmpty(): GetTpayTransactionsChannels;
}

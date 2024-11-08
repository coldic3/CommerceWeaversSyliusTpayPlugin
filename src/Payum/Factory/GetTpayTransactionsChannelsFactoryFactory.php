<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\GetTpayTransactionsChannels;
use Payum\Core\Model\ArrayObject;

final class GetTpayTransactionsChannelsFactoryFactory implements GetTpayTransactionsChannelsFactoryInterface
{
    public function createNewEmpty(): GetTpayTransactionsChannels
    {
        return new GetTpayTransactionsChannels(new ArrayObject());
    }
}

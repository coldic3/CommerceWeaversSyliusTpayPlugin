<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;
use Payum\Core\Model\ArrayObject;

final class GetTpayTransactionsChannelsFactory implements GetTpayTransactionsChannelsFactoryInterface
{
    public function createNewEmpty(): GetTpayTransactionsChannels
    {
        return new GetTpayTransactionsChannels(new ArrayObject());
    }
}

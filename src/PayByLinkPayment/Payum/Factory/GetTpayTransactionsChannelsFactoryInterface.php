<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;

interface GetTpayTransactionsChannelsFactoryInterface
{
    public function createNewEmpty(): GetTpayTransactionsChannels;
}

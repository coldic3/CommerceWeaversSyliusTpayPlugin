<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\BaseApiAwareAction;

class GetTpayTransactionsChannelsAction extends BaseApiAwareAction
{
    /**
     * @param GetTpayTransactionsChannels $request
     */
    public function execute($request): void
    {
        $result = $this->api->transactions()->getChannels();

        $request->setResult($result);
    }

    public function supports($request): bool
    {
        return $request instanceof GetTpayTransactionsChannels;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\FetchPaymentDetails;
use Payum\Core\Bridge\Spl\ArrayObject;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class FetchPaymentDetailsAction extends BaseApiAwareAction
{
    /**
     * @param FetchPaymentDetails $request
     */
    public function execute($request): void
    {
        $response = $this->api->transactions()->getTransactionById($request->getTransactionId());

        /** @var ArrayObject $model */
        $model = $request->getModel();
        $model->replace($response);
    }

    public function supports($request): bool
    {
        return $request instanceof FetchPaymentDetails && $request->getModel() instanceof \ArrayAccess;
    }
}

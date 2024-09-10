<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\PayWithCard;
use Payum\Core\Reply\HttpRedirect;
use Sylius\Component\Core\Model\PaymentInterface;

class PayWithCardAction extends BaseApiAwareAction
{
    /**
     * @param PayWithCard $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();
        $details = $model->getDetails();

        $response = $this->api->transactions()->createPaymentByTransactionId([
            'groupId' => 103,
            'cardPaymentData' => [
                'card' => $details['tpay']['card'],
            ],
        ], $details['tpay']['transaction_id']);

        $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];
        unset($details['tpay']['card']);

        $model->setDetails($details);

        if ($response['status'] === 'pending') {
            throw new HttpRedirect($details['tpay']['transaction_payment_url']);
        }
    }

    public function supports($request): bool
    {
        return $request instanceof PayWithCard && $request->getModel() instanceof PaymentInterface;
    }
}

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

        unset($details['tpay']['card']);

        if ('failed' === $response['result']) {
            $details['tpay']['status'] = PaymentInterface::STATE_FAILED;
        }

        if ('success' === $response['result'] && 'pending' === $response['status']) {
            $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];

            $model->setDetails($details);

            throw new HttpRedirect($details['tpay']['transaction_payment_url']);
        }

        $model->setDetails($details);
    }

    public function supports($request): bool
    {
        return $request instanceof PayWithCard && $request->getModel() instanceof PaymentInterface;
    }
}

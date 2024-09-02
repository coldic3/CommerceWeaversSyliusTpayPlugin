<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class CreateTransactionAction extends BaseApiAwareAction
{
    /**
     * @param CreateTransaction $request
     */
    public function execute($request): void
    {

        /** @var PaymentInterface $model */
        $model = $request->getModel();
        $details = $model->getDetails();

        $order = $model->getOrder();
        $customer = $order->getCustomer();

        $response = $this->api->transactions()->createTransaction([
            'amount' => number_format($model->getAmount() / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $customer->getFullName(),
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $request->getAfterUrl(),
                    'error' => $request->getAfterUrl(),
                ],
            ],
        ]);

        $details['tpay']['transaction_id'] = $response['transactionId'];
        $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];

        $model->setDetails($details);
    }

    public function supports($request): bool
    {
        return $request instanceof CreateTransaction && $request->getModel() instanceof PaymentInterface;
    }
}

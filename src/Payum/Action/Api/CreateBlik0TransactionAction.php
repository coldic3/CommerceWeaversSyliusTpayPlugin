<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateBlik0Transaction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class CreateBlik0TransactionAction extends BaseApiAwareAction
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
        $billingAddress = $order->getBillingAddress();

        $blikToken = $model->getDetails()['blik'];

        $response = $this->api->transactions()->createTransaction([
            'amount' => number_format($model->getAmount() / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'pay' => [
                'groupId' => 150,
                'blikPaymentData' => [
                    'blikToken' => $blikToken,
                ],
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $request->getAfterUrl(),
                    'error' => $request->getAfterUrl(),
                ],
            ],
        ]);

        unset($details['blik']);
        $details['tpay']['transaction_id'] = $response['transactionId'];

        $model->setDetails($details);
    }

    public function supports($request): bool
    {
        return $request instanceof CreateBlik0Transaction && $request->getModel() instanceof PaymentInterface;
    }
}

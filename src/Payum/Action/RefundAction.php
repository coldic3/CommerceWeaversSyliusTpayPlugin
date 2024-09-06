<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\BaseApiAwareAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\RefundCannotBeMadeException;
use Payum\Core\Request\Refund;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class RefundAction extends BaseApiAwareAction
{
    /**
     * @param Refund $request
     */
    public function execute(mixed $request): void
    {
        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        /** @var array{tpay: array{transaction_id?: string}} $details */
        $details = $payment->getDetails();

        $transactionId = $details['tpay']['transaction_id'] ?? null;

        if (null === $transactionId) {
            throw new RefundCannotBeMadeException('Tpay transaction id cannot be found.');
        }

        $this->api->transactions()->createRefundByTransactionId([], $transactionId);
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Refund && $request->getModel() instanceof PaymentInterface;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateBlik0Transaction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Sylius\Component\Core\Model\PaymentInterface;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Capture $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();

        if ($this->transactionIsBlik($model)) {
            $this->gateway->execute(
                new CreateBlik0Transaction($request->getToken()->getAfterUrl(), $model),
            );

            return;
        }

        $this->gateway->execute(
            new CreateTransaction($request->getToken()->getAfterUrl(), $model),
        );

        $paymentDetails = $model->getDetails();

        throw new HttpRedirect($paymentDetails['tpay']['transaction_payment_url']);
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }

    private function transactionIsBlik($model): bool
    {
        return array_key_exists('blik', $model->getDetails());
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

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
}

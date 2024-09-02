<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\FetchPaymentDetails;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class GetStatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param GetStatusInterface $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getFirstModel();
        $paymentDetails = $model->getDetails();

        $this->gateway->execute(
            new FetchPaymentDetails($paymentDetails['tpay']['transaction_id'], $fetchedPaymentDetails = new ArrayObject()),
        );

        $paymentDetails['tpay']['status'] = $fetchedPaymentDetails->offsetGet('status');

        $model->setDetails($paymentDetails);

        switch ($paymentDetails['tpay']['status']) {
            case 'correct':
                $request->markCaptured();
                break;
        }
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getFirstModel() instanceof PaymentInterface;
    }
}

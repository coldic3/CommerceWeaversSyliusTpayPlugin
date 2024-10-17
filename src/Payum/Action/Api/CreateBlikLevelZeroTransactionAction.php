<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateBlikLevelZeroPaymentPayloadFactoryInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;

final class CreateBlikLevelZeroTransactionAction extends BasePaymentAwareAction
{
    use GenericTokenFactoryAwareTrait;

    public function __construct(
        private readonly CreateBlikLevelZeroPaymentPayloadFactoryInterface $createBlikLevelZeroPaymentPayloadFactory,
        private readonly NotifyTokenFactoryInterface $notifyTokenFactory,
    ) {
        parent::__construct();
    }

    protected function doExecute(Generic $request, PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        $notifyToken = $this->notifyTokenFactory->create($model, $gatewayName, $localeCode);

        $this->do(
            fn () => $this->api->transactions()->createTransaction(
                $this->createBlikLevelZeroPaymentPayloadFactory->createFrom($model, $notifyToken->getTargetUrl(), $localeCode),
            ),
            onSuccess: function (array $response) use ($paymentDetails) {
                $paymentDetails->setTransactionId($response['transactionId']);
                $paymentDetails->setStatus($response['status']);
            },
            onFailure: fn () => $paymentDetails->setStatus(PaymentInterface::STATE_FAILED),
        );
    }

    public function supports($request): bool
    {
        if (!$request instanceof CreateTransaction) {
            return false;
        }

        $model = $request->getModel();

        if (!$model instanceof PaymentInterface) {
            return false;
        }

        $paymentDetails = PaymentDetails::fromArray($model->getDetails());

        return $paymentDetails->isBlik();
    }
}

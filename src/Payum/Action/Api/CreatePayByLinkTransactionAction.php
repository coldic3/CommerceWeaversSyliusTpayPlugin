<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreatePayByLinkPayloadFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PaymentType;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Sylius\Component\Core\Model\PaymentInterface;

final class CreatePayByLinkTransactionAction extends AbstractCreateTransactionAction implements GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function __construct(
        private readonly CreatePayByLinkPayloadFactoryInterface $createPayByLinkPayloadFactory,
        private readonly NotifyTokenFactoryInterface $notifyTokenFactory,
    ) {
        parent::__construct();
    }

    /**
     * @param CreateTransaction $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();
        $gatewayName = $request->getToken()?->getGatewayName() ?? $this->getGatewayNameFrom($model);
        $localeCode = $this->getLocaleCodeFrom($model);
        $notifyToken = $this->notifyTokenFactory->create($model, $gatewayName, $localeCode);
        $paymentDetails = PaymentDetails::fromArray($model->getDetails());

        $this->do(
            fn () => $this->api->transactions()->createTransaction(
                $this->createPayByLinkPayloadFactory->createFrom($model, $notifyToken->getTargetUrl(), $localeCode),
            ),
            onSuccess: function (array $response) use ($paymentDetails) {
                $paymentDetails->setTransactionId($response['transactionId']);
                $paymentDetails->setStatus($response['status']);
                $paymentDetails->setPaymentUrl($response['transactionPaymentUrl']);
            },
            onFailure: fn () => $paymentDetails->setStatus(PaymentInterface::STATE_FAILED),
        );

        $model->setDetails($paymentDetails->toArray());

        if ($paymentDetails->getPaymentUrl() !== null) {
            throw new HttpRedirect($paymentDetails->getPaymentUrl());
        }
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

        return $paymentDetails->getType() === PaymentType::PAY_BY_LINK;
    }
}

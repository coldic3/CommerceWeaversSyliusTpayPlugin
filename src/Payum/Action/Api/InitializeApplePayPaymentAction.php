<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateInitializeApplePayPaymentPayloadFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;

final class InitializeApplePayPaymentAction extends AbstractCreateTransactionAction
{
    use GenericTokenFactoryAwareTrait;

    public function __construct(
        private readonly CreateInitializeApplePayPaymentPayloadFactoryInterface $createInitializeApplePayPaymentPayloadFactory,
    ) {
        parent::__construct();
    }

    /**
     * @param InitializeApplePayPayment $request
     */
    public function execute($request): void
    {
        /** @var ArrayObject $model */
        $model = $request->getModel();

        $this->do(
            fn () => $this->api->applePay()->init($this->createInitializeApplePayPaymentPayloadFactory->create($model)),
            onSuccess: fn (array $response) => $request->getOutput()->replace($response),
            onFailure: fn () => $request->getOutput()->replace(['result' => PaymentInterface::STATE_FAILED]),
        );
    }

    public function supports($request): bool
    {
        return $request instanceof InitializeApplePayPayment;
    }
}

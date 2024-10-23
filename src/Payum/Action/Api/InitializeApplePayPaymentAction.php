<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateInitializeApplePayPaymentPayloadFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Generic;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;

final class InitializeApplePayPaymentAction extends BasePaymentAwareAction
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
    protected function doExecute(Generic $request, PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        $this->do(
            fn () => $this->api->applePay()->init(
                $this->createInitializeApplePayPaymentPayloadFactory->create(new ArrayObject([
                    'domainName' => $request->getDomainName(),
                    'displayName' => $request->getDisplayName(),
                    'validationUrl' => $request->getValidationUrl(),
                ])),
            ),
            onSuccess: function (array $response) use ($paymentDetails) {
                $paymentDetails->setApplePaySession($response['session']);
            },
            onFailure: fn () => $paymentDetails->setStatus(PaymentInterface::STATE_FAILED),
        );
    }

    public function supports($request): bool
    {
        return $request instanceof InitializeApplePayPayment;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Controller;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class InitApplePayPaymentAction
{
    public function __construct(
        private readonly Payum $payum,
        private readonly CartContextInterface $cartContext,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();
        /** @var PaymentInterface $payment */
        $payment = $cart->getPayments()->last();
        $gateway = $this->getGateway();

        $domainName = $request->request->get('domainName');
        Assert::string($domainName);
        $displayName = $request->request->get('displayName');
        Assert::string($displayName);
        $validationUrl = $request->request->get('validationUrl');
        Assert::string($validationUrl);

        $gateway->execute(new InitializeApplePayPayment(
            $payment,
            $domainName,
            $displayName,
            $validationUrl,
        ));

        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        return new JsonResponse(['session' => $paymentDetails->getApplePaySession()]);
    }

    private function getGateway(): GatewayInterface
    {
        return $this->payum->getGateway('tpay');
    }
}

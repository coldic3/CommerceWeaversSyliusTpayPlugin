<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class CreateTransactionAction extends BaseApiAwareAction
{
    public function __construct (
        private RouterInterface $router,
        private Payum $payum,
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
        $details = $model->getDetails();

        $order = $model->getOrder();
        $localeCode = $order->getLocaleCode();
        $customer = $order->getCustomer();
        $billingAddress = $order->getBillingAddress();

        $notifyToken = $this->createNotifyToken($model, $localeCode);

        $response = $this->api->transactions()->createTransaction([
            'amount' => number_format($model->getAmount() / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $this->router->generate('sylius_shop_order_thank_you', ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
                    'error' => $this->router->generate('sylius_shop_order_thank_you', ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
                ],
                'notification' => [
                    'url' => $notifyToken->getTargetUrl(),
                ],
            ],
        ]);

        $details['tpay']['transaction_id'] = $response['transactionId'];
        $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];

        $model->setDetails($details);
    }

    private function createNotifyToken(PaymentInterface $payment, string $localeCode): TokenInterface
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $factory = $this->payum->getTokenFactory();

        return $factory->createToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            $this->router->generate(
                'commerce_weavers_tpay_payment_notification',
                ['_locale' => $localeCode],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
        );
    }

    public function supports($request): bool
    {
        return $request instanceof CreateTransaction && $request->getModel() instanceof PaymentInterface;
    }
}

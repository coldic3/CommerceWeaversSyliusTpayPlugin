<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tpay\OpenApi\Api\TpayApi;

/**
 * @property TpayApi $api
 */
final class CreateTransactionAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;

    public function __construct (
        private RouterInterface $router,
        private string $successRoute,
        private string $errorRoute,
        private string $notifyRoute,
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

        $notifyToken = $this->createNotifyToken($model, $request->getToken(), $localeCode);

        // TODO: extract the create transaction payload creating, so we can reuse it e.g. during the blik level 0 payment
        $response = $this->api->transactions()->createTransaction([
            'amount' => number_format($model->getAmount() / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $this->router->generate($this->successRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
                    'error' => $this->router->generate($this->errorRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
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

    private function createNotifyToken(PaymentInterface $payment, TokenInterface $token, string $localeCode): TokenInterface
    {
        return $this->tokenFactory->createToken(
            $token->getGatewayName(),
            $payment,
            $this->router->generate($this->notifyRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
        );
    }

    public function supports($request): bool
    {
        return $request instanceof CreateTransaction && $request->getModel() instanceof PaymentInterface;
    }
}

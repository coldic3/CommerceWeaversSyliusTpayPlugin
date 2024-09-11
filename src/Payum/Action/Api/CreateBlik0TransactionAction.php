<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateBlik0Transaction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tpay\OpenApi\Api\TpayApi;
use Webmozart\Assert\Assert;

/**
 * @property TpayApi $api
 */
final class CreateBlik0TransactionAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;

    public function __construct(
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
        $token = $request->getToken();
        Assert::notNull($token);

        $order = $model->getOrder();
        Assert::notNull($order);
        $customer = $order->getCustomer();
        Assert::notNull($customer);
        $localeCode = $order->getLocaleCode();
        Assert::notNull($localeCode);
        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);
        $notifyToken = $this->createNotifyToken($model, $token, $localeCode);
        $amount = $model->getAmount();
        Assert::notNull($amount);

        $blikToken = $model->getDetails()['tpay']['blik'];

        $response = $this->api->transactions()->createTransaction([
            'amount' => number_format($amount / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'pay' => [
                'groupId' => 150,
                'blikPaymentData' => [
                    'blikToken' => $blikToken,
                ],
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
        $details['tpay']['status'] = $response['status'];

        $model->setDetails($details);
    }

    public function supports($request): bool
    {
        return $request instanceof CreateBlik0Transaction && $request->getModel() instanceof PaymentInterface;
    }

    private function createNotifyToken(PaymentInterface $payment, TokenInterface $token, string $localeCode): TokenInterface
    {
        return $this->tokenFactory->createToken(
            $token->getGatewayName(),
            $payment,
            $this->router->generate($this->notifyRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
        );
    }
}

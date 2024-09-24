<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class CreateRedirectBasedPaymentPayloadFactory implements CreateRedirectBasedPaymentPayloadFactoryInterface
{
    private const TPAY_FIELD = 'tpay';

    private const SUCCESS_URL_FIELD = 'successUrl';

    private const FAILURE_URL_FIELD = 'failureUrl';

    public function __construct(
        private RouterInterface $router,
        private string $successRoute,
        private string $errorRoute,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createFrom(PaymentInterface $payment, string $notifyUrl, string $localeCode): array
    {
        $order = $payment->getOrder();
        Assert::notNull($order);
        $customer = $order->getCustomer();
        Assert::notNull($customer);
        $billingAddress = $order->getBillingAddress();
        Assert::notNull($billingAddress);
        $amount = $payment->getAmount();
        Assert::notNull($amount);

        return [
            'amount' => number_format($amount / 100, 2, thousands_separator: ''),
            'description' => sprintf('zamówienie #%s', $order->getNumber()), // TODO: Introduce translations
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $this->getSuccessUrl($payment, $localeCode),
                    'error' => $this->getFailureUrl($payment, $localeCode),
                ],
                'notification' => [
                    'url' => $notifyUrl,
                ],
            ],
        ];
    }

    private function getSuccessUrl(PaymentInterface $payment, string $localeCode): string
    {
        $paymentDetails = $payment->getDetails();

        if (!isset($paymentDetails[self::TPAY_FIELD][self::SUCCESS_URL_FIELD])) {
            return $this->router->generate($this->successRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $paymentDetails[self::TPAY_FIELD][self::SUCCESS_URL_FIELD];
    }

    private function getFailureUrl(PaymentInterface $payment, string $localeCode): string
    {
        $paymentDetails = $payment->getDetails();

        if (!isset($paymentDetails[self::TPAY_FIELD][self::FAILURE_URL_FIELD])) {
            return $this->router->generate($this->errorRoute, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $paymentDetails[self::TPAY_FIELD][self::FAILURE_URL_FIELD];
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator\CallbackUrlGeneratorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class CreateRedirectBasedPaymentPayloadFactory implements CreateRedirectBasedPaymentPayloadFactoryInterface
{
    public function __construct(
        private readonly CallbackUrlGeneratorInterface $callbackUrlGenerator,
        private readonly TranslatorInterface $translator,
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
            'description' => $this->translator->trans(
                'commerce_weavers_sylius_tpay.tpay.transaction_description',
                ['%orderNumber%' => $order->getNumber()],
            ),
            'payer' => [
                'email' => $customer->getEmail(),
                'name' => $billingAddress->getFullName(),
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $this->callbackUrlGenerator->generateSuccessUrl($payment, $localeCode),
                    'error' => $this->callbackUrlGenerator->generateFailureUrl($payment, $localeCode),
                ],
                'notification' => [
                    'url' => $notifyUrl,
                ],
            ],
        ];
    }
}

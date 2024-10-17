<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Sylius\Component\Core\Model\PaymentInterface;

final class CreateApplePayPaymentPayloadFactory implements CreateApplePayPaymentPayloadFactoryInterface
{
    public function __construct(
        private readonly CreateRedirectBasedPaymentPayloadFactoryInterface $createRedirectBasedPaymentPayloadFactory,
    ) {
    }

    public function createFrom(PaymentInterface $payment, string $notifyUrl, string $localeCode): array
    {
        /** @var array{pay: array<string, mixed>} $payload */
        $payload = $this->createRedirectBasedPaymentPayloadFactory->createFrom($payment, $notifyUrl, $localeCode);

        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $applePayToken = $paymentDetails->getApplePayToken() ?? throw new \InvalidArgumentException('The given payment does not have an Apple Pay token.');

        $payload['pay']['groupId'] = PayGroup::APPLE_PAY;
        $payload['pay']['applePayPaymentData'] = $applePayToken;

        return $payload;
    }
}

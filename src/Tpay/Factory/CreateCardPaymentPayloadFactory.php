<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\VarDumper\VarDumper;

final class CreateCardPaymentPayloadFactory implements CreateCardPaymentPayloadFactoryInterface
{
    public function __construct(
        private CreateRedirectBasedPaymentPayloadFactoryInterface $createRedirectBasedPaymentPayloadFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function createFrom(PaymentInterface $payment, string $notifyUrl, string $localeCode, bool $tokenizeCard = false): array
    {
        /** @var array{pay: array<string, mixed>} $payload */
        $payload = $this->createRedirectBasedPaymentPayloadFactory->createFrom($payment, $notifyUrl, $localeCode);

        $payload['pay']['groupId'] = PayGroup::CARD;

        if ($tokenizeCard) {
            $payload['pay']['cardPaymentData']['save'] = true;
        }

        VarDumper::dump($payload);
        VarDumper::dump($payment);

        return $payload;
    }
}

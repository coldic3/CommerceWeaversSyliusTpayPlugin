<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class CallbackUrlGenerator implements CallbackUrlGeneratorInterface
{
    private const LOCALE_PARAMETER = '_locale';

    private const ORDER_TOKEN_PARAMETER = 'orderToken';

    public function __construct(
        private readonly RouterInterface $router,
        private readonly string $successRoute,
        private readonly string $failureRoute,
    ) {
    }

    public function generateSuccessUrl(PaymentInterface $payment, string $localeCode): string
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        if ($paymentDetails->getSuccessUrl() !== null) {
            return $paymentDetails->getSuccessUrl();
        }

        $orderToken = $this->getOrderTokenFrom($payment);

        return $this->router->generate(
            $this->successRoute,
            [self::LOCALE_PARAMETER => $localeCode, self::ORDER_TOKEN_PARAMETER => $orderToken],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    public function generateFailureUrl(PaymentInterface $payment, string $localeCode): string
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        if ($paymentDetails->getFailureUrl() !== null) {
            return $paymentDetails->getFailureUrl();
        }

        $orderToken = $this->getOrderTokenFrom($payment);

        return $this->router->generate(
            $this->failureRoute,
            [self::LOCALE_PARAMETER => $localeCode, self::ORDER_TOKEN_PARAMETER => $orderToken],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    private function getOrderTokenFrom(PaymentInterface $payment): string
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        /** @var string $orderToken */
        $orderToken = $order->getTokenValue();

        return $orderToken;
    }
}

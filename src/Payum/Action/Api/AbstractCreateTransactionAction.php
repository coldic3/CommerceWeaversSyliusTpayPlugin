<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use Payum\Core\GatewayAwareTrait;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

abstract class AbstractCreateTransactionAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;
    use GatewayAwareTrait;

    protected function getLocaleCodeFrom(PaymentInterface $payment): string
    {
        return $payment->getOrder()?->getLocaleCode() ?? throw new \InvalidArgumentException('Cannot determine locale code for a given payment');
    }

    protected function getGatewayNameFrom(PaymentInterface $payment): string
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();

        return $paymentMethod?->getGatewayConfig()?->getGatewayName() ?? throw new \InvalidArgumentException('Cannot determine gateway name for a given payment');
    }
}

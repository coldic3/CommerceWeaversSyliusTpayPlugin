<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommand;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByRedirect;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PayByRedirectFactory implements NextCommandFactoryInterface
{
    public function __construct(
        private readonly CypherInterface $cypher,
    ) {
    }

    public function create(Pay $command, PaymentInterface $payment): PayByRedirect
    {
        if (!$this->supports($command, $payment)) {
            throw new UnsupportedNextCommandFactory('This factory does not support the given command.');
        }

        /** @var int $paymentId */
        $paymentId = $payment->getId();

        return new PayByRedirect($paymentId);
    }

    public function supports(Pay $command, PaymentInterface $payment): bool
    {
        if ($payment->getId() === null) {
            return false;
        }

        $gatewayConfig = $this->getGatewayConfig($payment);

        if ($gatewayConfig instanceof CryptedInterface) {
            $gatewayConfig->decrypt($this->cypher);
        }

        if (null === $gatewayConfig) {
            return false;
        }

        $config = $gatewayConfig->getConfig();

        return isset($config['type']) && $config['type'] === 'redirect';
    }

    private function getGatewayConfig(PaymentInterface $payment): ?GatewayConfigInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();

        return $paymentMethod?->getGatewayConfig();
    }
}

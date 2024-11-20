<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Fixture\Factory;

use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class PaymentMethodExampleFactory implements ExampleFactoryInterface
{
    public const TPAY_BASED_PAYMENT_METHOD_PREFIX = 'tpay';

    public function __construct(
        private readonly ExampleFactoryInterface $decorated,
        private readonly CypherInterface $cypher,
    ) {
    }

    public function create(array $options = []): PaymentMethodInterface
    {
        /** @var PaymentMethodInterface|mixed $paymentMethod */
        $paymentMethod = $this->decorated->create($options);

        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        if (!str_starts_with($gatewayConfig->getGatewayName(), self::TPAY_BASED_PAYMENT_METHOD_PREFIX)) {
            return $paymentMethod;
        }

        if ($gatewayConfig instanceof CryptedInterface) {
            $gatewayConfig->encrypt($this->cypher);
        }

        return $paymentMethod;
    }
}

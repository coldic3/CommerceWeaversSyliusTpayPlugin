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
    public function __construct(
        private ExampleFactoryInterface $decorated,
        private CypherInterface $cypher,
    ) {
    }

    public function create(array $options = []): PaymentMethodInterface
    {
        /** @var PaymentMethodInterface|mixed $paymentMethod */
        $paymentMethod = $this->decorated->create($options);

        Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        if ($gatewayConfig->getGatewayName() !== 'Tpay') {
            return $paymentMethod;
        }

        if ($gatewayConfig instanceof CryptedInterface) {
            $gatewayConfig->encrypt($this->cypher);
        }

        return $paymentMethod;
    }
}

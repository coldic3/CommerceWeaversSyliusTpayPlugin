<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Model\OrderLastNewPaymentAwareInterface;
use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class BlikTokenRequiredValidator extends ConstraintValidator
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CypherInterface $cypher,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_object($value)) {
            throw new UnexpectedValueException($value, OrderInterface::class);
        }

        if (!is_a($value, Pay::class)) {
            throw new UnexpectedValueException($value, OrderInterface::class);
        }

        if (!is_a($constraint, BlikTokenRequired::class)) {
            throw new UnexpectedValueException($constraint, BlikTokenRequired::class);
        }

        if (null !== $value->blikToken) {
            return;
        }

        /** @var (OrderInterface&OrderLastNewPaymentAwareInterface)|null $order */
        $order = $this->orderRepository->findOneByTokenValue($value->orderToken);

        if (null === $order) {
            return;
        }

        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $payment) {
            return;
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();

        if (null === $paymentMethod) {
            return;
        }

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        if (null === $gatewayConfig) {
            return;
        }

        if (is_a($gatewayConfig, CryptedInterface::class)) {
            $gatewayConfig->decrypt($this->cypher);
        }

        /** @var array{type?: string} $config */
        $config = $gatewayConfig->getConfig();

        if (!isset($config['type']) || 'blik' !== $config['type']) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('blikToken')
            ->setCode($constraint::BLIK_TOKEN_REQUIRED_ERROR)
            ->addViolation()
        ;
    }
}

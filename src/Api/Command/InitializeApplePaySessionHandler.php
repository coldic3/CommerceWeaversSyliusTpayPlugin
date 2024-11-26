<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Exception\OrderCannotBeFoundException;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\Exception\PaymentCannotBeFoundException;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Payum\Core\GatewayInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class InitializeApplePaySessionHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly GatewayInterface $gateway,
        private readonly InitializeApplePayPaymentFactoryInterface $initializeApplePayPaymentFactory,
    ) {
    }

    public function __invoke(InitializeApplePaySession $command): InitializeApplePaySessionResult
    {
        $order = $this->findOrderOr404($command->orderToken);
        $payment = $this->findPaymentOr404($order, $command->paymentId);

        $this->gateway->execute(
            $this->initializeApplePayPaymentFactory->createNewWithModelAndOutput(
                $payment,
                $command->domainName,
                $command->displayName,
                $command->validationUrl,
            ),
        );

        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        Assert::notNull($paymentDetails->getApplePaySession());

        return new InitializeApplePaySessionResult($paymentDetails->getApplePaySession());
    }

    private function findOrderOr404(string $orderToken): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($orderToken);

        if (null === $order) {
            throw new OrderCannotBeFoundException(sprintf('Order with token "%s" cannot be found.', $orderToken));
        }

        return $order;
    }

    private function findPaymentOr404(OrderInterface $order, int $paymentId): PaymentInterface
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneBy([
            'id' => $paymentId,
            'order' => $order,
        ]);

        if (null === $payment) {
            throw new PaymentCannotBeFoundException(sprintf('Payment with id "%s" cannot be found.', $paymentId));
        }

        return $payment;
    }
}

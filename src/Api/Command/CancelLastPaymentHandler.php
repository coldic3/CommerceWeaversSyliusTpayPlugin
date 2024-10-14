<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Payment\Canceller\PaymentCancellerInterface;
use CommerceWeavers\SyliusTpayPlugin\Payment\Checker\PaymentCancellationPossibilityCheckerInterface;
use CommerceWeavers\SyliusTpayPlugin\Payment\Exception\PaymentCannotBeCancelledException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CancelLastPaymentHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentCancellationPossibilityCheckerInterface $paymentCancellationPossibilityChecker,
        private readonly PaymentCancellerInterface $paymentCanceller,
    ) {
    }

    public function __invoke(CancelLastPayment $command): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($command->orderToken);

        if ($order === null) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" not found.', $command->orderToken));
        }

        $payment = $order->getLastPayment();

        if ($payment === null) {
            throw new NotFoundHttpException(sprintf('The last payment for order with token "%s" not found.', $command->orderToken));
        }

        if (!$this->paymentCancellationPossibilityChecker->canBeCancelled($payment)) {
            throw new PaymentCannotBeCancelledException($payment);
        }

        $this->paymentCanceller->cancel($payment);
    }
}

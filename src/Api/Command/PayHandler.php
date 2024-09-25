<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class PayHandler
{
    use HandleTrait;

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly NextCommandFactoryInterface $nextCommandFactory,
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Pay $command): PayResult
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($command->orderToken);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" cannot be found.', $command->orderToken));
        }

        $lastPayment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $lastPayment) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not have a new payment.', $command->orderToken));
        }

        $this->setPaymentDetails($lastPayment, ['successUrl' => $command->successUrl, 'failureUrl' => $command->failureUrl]);

        $nextCommand = $this->nextCommandFactory->create($command, $lastPayment);

        $nextCommandResult = $this->handle($nextCommand);

        if (!$nextCommandResult instanceof PayResult) {
            throw new \TypeError(sprintf('Expected instance of %s, but got %s', PayResult::class, get_debug_type($nextCommandResult)));
        }

        return $nextCommandResult;
    }

    private function setPaymentDetails(PaymentInterface $payment, array $details): void
    {
        $payment->setDetails(array_merge(
            $payment->getDetails(),
            $details,
        ));
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Exception\UnresolvableNextCommandException;
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

        $nextCommand = match (true) {
            $command->blikToken !== null => new PayByBlik($lastPayment->getId(), $command->blikToken),
            default => throw new UnresolvableNextCommandException('Provided command does not contain a valid payment data.'),
        };

        $nextCommandResult = $this->handle($nextCommand);

        if (!$nextCommandResult instanceof PayResult) {
            throw new \TypeError(sprintf('Expected instance of %s, but got %s', PayResult::class, get_debug_type($nextCommandResult)));
        }

        return $nextCommandResult;
    }
}

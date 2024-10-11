<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payment\Checker;

use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentCancellationPossibilityChecker implements PaymentCancellationPossibilityCheckerInterface
{
    public function __construct(
        private readonly StateMachineInterface|null $stateMachine,
        private readonly FactoryInterface $stateMachineFactory,
    ) {
    }

    public function canBeCancelled(PaymentInterface $payment): bool
    {
        return $this->getStateMachine()->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL);
    }

    private function getStateMachine(): StateMachineInterface
    {
        if (null !== $this->stateMachine) {
            return $this->stateMachine;
        }

        return new WinzouStateMachineAdapter($this->stateMachineFactory);
    }
}

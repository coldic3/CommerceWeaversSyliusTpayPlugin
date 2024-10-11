<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payment\Canceller;

use CommerceWeavers\SyliusTpayPlugin\Payment\Exception\PaymentCannotBeCancelledException;
use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentCanceller implements PaymentCancellerInterface
{
    public function __construct(
        private readonly StateMachineInterface|null $stateMachine,
        private readonly FactoryInterface $stateMachineFactory,
    ) {
    }

    public function cancel(PaymentInterface $payment): void
    {
        try {
            $this->getStateMachine()->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL);
        } catch (StateMachineExecutionException) {
            throw new PaymentCannotBeCancelledException($payment);
        }
    }

    private function getStateMachine(): StateMachineInterface
    {
        if (null !== $this->stateMachine) {
            return $this->stateMachine;
        }

        return new WinzouStateMachineAdapter($this->stateMachineFactory);
    }
}

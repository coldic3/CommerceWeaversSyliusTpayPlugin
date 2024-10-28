<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByBlikHandler extends AbstractPayByHandler
{
    public function __invoke(PayByBlik $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->blikToken);
        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment, isRedirectedBased: false);
    }

    private function setTransactionData(PaymentInterface $payment, string $blikToken): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setBlikToken($blikToken);

        $payment->setDetails($paymentDetails->toArray());
    }
}

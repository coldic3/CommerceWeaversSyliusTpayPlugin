<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayBySavedCardHandler extends AbstractPayByHandler
{
    public function __invoke(PayBySavedCard $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->savedCardId);

        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment);
    }

    private function setTransactionData(PaymentInterface $payment, int $savedCardId): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setUseSavedCreditCard($savedCardId);

        $payment->setDetails($paymentDetails->toArray());
    }
}

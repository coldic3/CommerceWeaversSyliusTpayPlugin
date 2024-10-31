<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByCardHandler extends AbstractPayByHandler
{
    public function __invoke(PayByCard $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->encodedCardData, $command->saveCard);
        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment);
    }

    private function setTransactionData(PaymentInterface $payment, string $encodedCardData, bool $saveCard): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setEncodedCardData($encodedCardData);
        $paymentDetails->setSaveCreditCardForLater($saveCard);

        $payment->setDetails($paymentDetails->toArray());
    }
}

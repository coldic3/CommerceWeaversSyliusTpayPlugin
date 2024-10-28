<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByVisaMobileHandler extends AbstractPayByHandler
{
    public function __invoke(PayByVisaMobile $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->visaMobilePhoneNumber);
        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment, false);
    }

    private function setTransactionData(PaymentInterface $payment, string $phoneNumber): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setVisaMobilePhoneNumber($phoneNumber);

        $payment->setDetails($paymentDetails->toArray());
    }
}

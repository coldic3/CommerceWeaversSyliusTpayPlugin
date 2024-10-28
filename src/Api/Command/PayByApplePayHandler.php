<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByApplePayHandler extends AbstractPayByHandler
{
    public function __invoke(PayByApplePay $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->applePayToken);
        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment, isRedirectedBased: false);
    }

    private function setTransactionData(PaymentInterface $payment, string $applePayToken): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setApplePayToken($applePayToken);

        $payment->setDetails($paymentDetails->toArray());
    }
}

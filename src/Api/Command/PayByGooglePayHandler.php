<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByGooglePayHandler extends AbstractPayByHandler
{
    public function __invoke(PayByGooglePay $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->googlePayToken);
        $this->createTransactionProcessor->process($payment);

        return $this->createResultFrom($payment, isRedirectedBased: false);
    }

    private function setTransactionData(PaymentInterface $payment, string $googlePayToken): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setGooglePayToken($googlePayToken);

        $payment->setDetails($paymentDetails->toArray());
    }
}

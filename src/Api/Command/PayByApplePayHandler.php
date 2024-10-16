<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class PayByApplePayHandler extends AbstractPayByHandler
{
    public function __invoke(PayByApplePay $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->applePayToken);
        $this->createTransaction($payment);

        return $this->createResultFrom($payment);
    }

    private function setTransactionData(PaymentInterface $payment, string $applePayToken): void
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());
        $paymentDetails->setApplePayToken($applePayToken);

        $payment->setDetails($paymentDetails->toArray());
    }

    private function createResultFrom(PaymentInterface $payment): PayResult
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        Assert::notNull($paymentDetails->getStatus(), 'Payment status is required to create a result.');

        return new PayResult(
            $paymentDetails->getStatus(),
            $paymentDetails->getPaymentUrl(),
        );
    }
}

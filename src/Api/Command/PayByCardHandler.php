<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByCardHandler extends AbstractPayByHandler
{
    public function __invoke(PayByCard $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->encodedCardData);
        $this->createTransaction($payment);

        return $this->createResultFrom($payment);
    }

    private function setTransactionData(PaymentInterface $payment, string $encodedCardData): void
    {
        $details = $payment->getDetails();
        $details['tpay']['card'] = $encodedCardData;
        $payment->setDetails($details);
    }

    private function createResultFrom(PaymentInterface $payment): PayResult
    {
        $details = $payment->getDetails();

        return new PayResult(
            $details['tpay']['status'],
            $details['tpay']['transaction_payment_url'],
        );
    }
}

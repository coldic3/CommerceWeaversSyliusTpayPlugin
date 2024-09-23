<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayByBlikHandler extends AbstractPayByHandler
{
    public function __invoke(PayByBlik $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setTransactionData($payment, $command->blikToken);
        $this->createTransaction($payment);

        return $this->createResultFrom($payment);
    }

    private function setTransactionData(PaymentInterface $payment, string $blikToken): void
    {
        $details = $payment->getDetails();
        $details['tpay']['blik_token'] = $blikToken;
        $payment->setDetails($details);
    }

    private function createResultFrom(PaymentInterface $payment): PayResult
    {
        $details = $payment->getDetails();

        return new PayResult($details['tpay']['status']);
    }
}

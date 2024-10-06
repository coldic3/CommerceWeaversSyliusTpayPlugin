<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final class PayByRedirectHandler extends AbstractPayByHandler
{
    public function __invoke(PayByRedirect $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->createTransaction($payment);

        return $this->createResultFrom($payment);
    }

    private function createResultFrom(PaymentInterface $payment): PayResult
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        Assert::notNull($paymentDetails->getStatus(), 'Payment status is required to create a result.');
        Assert::notNull($paymentDetails->getPaymentUrl(), 'Payment URL is required to create a result.');

        return new PayResult(
            $paymentDetails->getStatus(),
            $paymentDetails->getPaymentUrl(),
        );
    }
}

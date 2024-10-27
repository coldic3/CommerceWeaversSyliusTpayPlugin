<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

abstract class AbstractPayByHandler
{
    public function __construct(
        protected readonly PaymentRepositoryInterface $paymentRepository,
        protected readonly CreateTransactionProcessorInterface $createTransactionProcessor,
    ) {
    }

    protected function findOr404(int $paymentId): PaymentInterface
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($paymentId);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Payment with id "%d" cannot be found.', $paymentId));
        }

        return $payment;
    }

    protected function createResultFrom(PaymentInterface $payment, bool $isRedirectedBased = true): PayResult
    {
        $paymentDetails = PaymentDetails::fromArray($payment->getDetails());

        Assert::notNull($paymentDetails->getStatus(), 'Payment status is required to create a result.');

        if ($isRedirectedBased) {
            Assert::notNull($paymentDetails->getPaymentUrl(), 'Payment URL is required to create a result.');
        }

        return new PayResult(
            $paymentDetails->getStatus(),
            $paymentDetails->getPaymentUrl(),
        );
    }
}

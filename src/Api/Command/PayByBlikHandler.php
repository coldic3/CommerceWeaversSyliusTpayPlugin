<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\CreateTransactionFactoryInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PayByBlikHandler
{
    public function __construct (
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly Payum $payum,
        private readonly CreateTransactionFactoryInterface $createTransactionFactory,
    ) {
    }

    public function __invoke(PayByBlik $command): PayResult
    {
        $payment = $this->findOr404($command->paymentId);

        $this->setBlikToken($payment, $command->blikToken);

        $gatewayName = $this->getGatewayName($payment);

        $this->payum->getGateway($gatewayName)->execute(
            $this->createTransactionFactory->createNewWithModel($payment),
            catchReply: true,
        );

        return $this->createResultFrom($payment);
    }

    private function findOr404(int $paymentId): PaymentInterface
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($paymentId);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Payment with id "%d" cannot be found.', $paymentId));
        }

        return $payment;
    }

    private function setBlikToken(PaymentInterface $payment, string $blikToken): void
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

    private function getGatewayName(PaymentInterface $payment): string
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        return $paymentMethod?->getGatewayConfig()?->getGatewayName() ?? throw new \InvalidArgumentException('Gateway name cannot be determined.');
    }
}

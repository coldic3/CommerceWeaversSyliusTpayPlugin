<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Command;

use Doctrine\Persistence\ObjectManager;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PayHandler
{
    public function __construct (
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly
        ObjectManager $paymentObjectManager,
        private readonly Payum $payum,
    ) {
    }

    public function __invoke(Pay $command): PayResult
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($command->orderToken);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" cannot be found.', $command->orderToken));
        }

        $lastPayment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $lastPayment) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not have a new payment.', $command->orderToken));
        }

        $lastPaymentDetails = $lastPayment->getDetails();

        match (true) {
            $command->blikToken !== null => $lastPaymentDetails['tpay']['blik_token'] = $command->blikToken,
            default => throw new \InvalidArgumentException('Missing blik token.'),
        };

        $lastPayment->setDetails($lastPaymentDetails);

        $token = $this->provideTokenBasedOnPayment($lastPayment);

        try {
            $this->payum->getGateway($token->getGatewayName())->execute(new Capture($token));
        } catch (ReplyInterface $reply) {
        }

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        $lastPaymentDetails = $lastPayment->getDetails();

        return new PayResult(
            $lastPaymentDetails['tpay']['status'],
        );
    }

    private function provideTokenBasedOnPayment(PaymentInterface $payment): TokenInterface
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        return $this->payum->getTokenFactory()->createCaptureToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            'https://commerceweavers.com',
        );
    }
}

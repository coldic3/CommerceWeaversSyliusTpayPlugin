<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Controller;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment as Twig;

final class DisplayPaymentFailedPageAction
{
    public function __construct(
        private readonly Twig $twig,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(Request $request, string $orderToken): Response
    {
        $order = $this->findOrderOr404($orderToken);
        $newPayment = $order->getLastPayment();
        $failedPayment = $order->getLastPayment(PaymentInterface::STATE_FAILED);

        $context = [
            'order' => $order,
            'payment' => $newPayment,
        ];

        if (null !== $failedPayment) {
            $paymentDetails = PaymentDetails::fromArray($failedPayment->getDetails());

            $context['errorMessage'] = $paymentDetails->getErrorMessage();
        }

        return new Response($this->twig->render(
            '@CommerceWeaversSyliusTpayPlugin/shop/cart/complete/payment_failed.html.twig',
            $context,
        ));
    }

    private function findOrderOr404(string $orderToken): OrderInterface
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($orderToken);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not exist.', $orderToken));
        }

        return $order;
    }
}

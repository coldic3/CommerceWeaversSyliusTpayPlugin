<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Routing;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class ResolveNextRouteAction implements ActionInterface, GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;

    /**
     * @param ResolveNextRoute $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();

        if ($model->getState() === PaymentInterface::STATE_COMPLETED) {
            $request->setRouteName('sylius_shop_order_thank_you');

            return;
        }

        if ($model->getState() === PaymentInterface::STATE_PROCESSING) {
            $request->setRouteName(Routing::SHOP_WAITING_FOR_PAYMENT);
            $request->setRouteParameters([
                'payum_token' => $this->createTokenForRoute(
                    $model,
                    Routing::SHOP_WAITING_FOR_PAYMENT,
                ),
            ]);

            return;
        }

        $orderToken = $model->getOrder()?->getTokenValue();

        Assert::notNull($orderToken, 'Order token must be present.');

        $request->setRouteName('sylius_shop_order_show');
        $request->setRouteParameters(['tokenValue' => $orderToken]);
    }

    private function createTokenForRoute(PaymentInterface $payment, string $route): string
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        $gatewayConfig = $paymentMethod?->getGatewayConfig();

        Assert::notNull($gatewayConfig, 'Payment method must have a gateway config.');

        return $this->tokenFactory->createToken(
            $gatewayConfig->getGatewayName(),
            $payment,
            $route,
        )->getHash();
    }

    public function supports($request): bool
    {
        return $request instanceof ResolveNextRoute && $request->getModel() instanceof PaymentInterface;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Controller;

use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment as Twig;

final class DisplayWaitingForPaymentPage
{
    public function __construct(
        private readonly Payum $payum,
        private readonly RouterInterface $router,
        private readonly ResolveNextRouteFactoryInterface $resolveNextRouteFactory,
        private readonly Twig $twig,
        private readonly int $refreshInterval,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $resolveNextRoute = $this->resolveNextRouteFactory->createNewWithModel($token);

        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        if (
            is_string($resolveNextRoute->getRouteName()) &&
            $resolveNextRoute->getRouteName() !== 'commerce_weavers_tpay_waiting_for_payment'
        ) {
            $this->payum->getHttpRequestVerifier()->invalidate($token);

            return new RedirectResponse(
                $this->router->generate($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters()),
            );
        }

        return new Response(
            $this->twig->render('@CommerceWeaversSyliusTpayPlugin/shop/cart/complete/waiting_for_payment.html.twig', [
                'refreshInterval' => $this->refreshInterval,
            ]),
        );
    }
}

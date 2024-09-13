<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token;

use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class NotifyTokenFactory implements NotifyTokenFactoryInterface
{
    public function __construct(
        private Payum $payum,
        private RouterInterface $router,
        private string $notifyRouteName,
    ) {
    }

    public function create(PaymentInterface $payment, string $gatewayName, string $localeCode): TokenInterface
    {
        return $this->payum->getTokenFactory()->createToken(
            $gatewayName,
            $payment,
            $this->router->generate($this->notifyRouteName, ['_locale' => $localeCode], UrlGeneratorInterface::ABSOLUTE_URL),
        );
    }
}

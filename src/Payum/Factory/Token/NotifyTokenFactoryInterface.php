<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token;

use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;

interface NotifyTokenFactoryInterface
{
    public function create(PaymentInterface $payment, string $gatewayName, string $localeCode): TokenInterface;
}

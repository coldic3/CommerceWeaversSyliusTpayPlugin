<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator;

use Sylius\Component\Core\Model\PaymentInterface;

interface CallbackUrlGeneratorInterface
{
    public function generateSuccessUrl(PaymentInterface $payment, string $localeCode): string;

    public function generateFailureUrl(PaymentInterface $payment, string $localeCode): string;
}

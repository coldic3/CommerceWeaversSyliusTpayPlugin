<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Request\InitializeApplePayPayment;
use Sylius\Component\Core\Model\PaymentInterface;

interface InitializeApplePayPaymentFactoryInterface
{
    public function createNewWithModelAndOutput(
        PaymentInterface $model,
        string $domainName,
        string $displayName,
        string $validationUrl,
    ): InitializeApplePayPayment;
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Request\InitializeApplePayPayment;
use Sylius\Component\Core\Model\PaymentInterface;

final class InitializeApplePayPaymentFactory implements InitializeApplePayPaymentFactoryInterface
{
    public function createNewWithModelAndOutput(
        PaymentInterface $model,
        string $domainName,
        string $displayName,
        string $validationUrl,
    ): InitializeApplePayPayment {
        return new InitializeApplePayPayment($model, $domainName, $displayName, $validationUrl);
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
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

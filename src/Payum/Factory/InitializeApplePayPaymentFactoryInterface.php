<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
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

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
use Payum\Core\Bridge\Spl\ArrayObject;

final class InitializeApplePayPaymentFactory implements InitializeApplePayPaymentFactoryInterface
{
    public function createNewWithModelAndOutput(ArrayObject $model, ArrayObject $output): InitializeApplePayPayment
    {
        return new InitializeApplePayPayment($model, $output);
    }
}

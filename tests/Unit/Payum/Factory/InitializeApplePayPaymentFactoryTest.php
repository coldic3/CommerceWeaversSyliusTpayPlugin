<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\InitializeApplePayPaymentFactory;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\InitializeApplePayPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\InitializeApplePayPayment;
use Payum\Core\Bridge\Spl\ArrayObject;
use PHPUnit\Framework\TestCase;

final class InitializeApplePayPaymentFactoryTest extends TestCase
{
    public function test_it_creates_an_initialize_apple_pay_payment_request(): void
    {
        $model = new ArrayObject();
        $output = new ArrayObject();

        $factory = $this->createTestSubject();

        $request = $factory->createNewWithModelAndOutput($model, $output);

        $this->assertInstanceOf(InitializeApplePayPayment::class, $request);
        $this->assertSame($model, $request->getModel());
        $this->assertSame($output, $request->getOutput());
    }

    private function createTestSubject(): InitializeApplePayPaymentFactoryInterface
    {
        return new InitializeApplePayPaymentFactory();
    }
}

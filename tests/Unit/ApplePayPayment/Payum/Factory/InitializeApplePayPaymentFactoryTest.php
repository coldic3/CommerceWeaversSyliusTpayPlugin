<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\ApplePayPayment\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactory;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Request\InitializeApplePayPayment;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Core\Model\PaymentInterface;

final class InitializeApplePayPaymentFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_creates_an_initialize_apple_pay_payment_request(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);

        $request = $this->createTestSubject()->createNewWithModelAndOutput(
            $payment->reveal(),
            'cw.nonexisting',
            'Commerce Weavers',
            'https://cw.nonexisting/validate',
        );

        $this->assertInstanceOf(InitializeApplePayPayment::class, $request);
        $this->assertSame($payment->reveal(), $request->getModel());
        $this->assertSame('cw.nonexisting', $request->getDomainName());
        $this->assertSame('Commerce Weavers', $request->getDisplayName());
        $this->assertSame('https://cw.nonexisting/validate', $request->getValidationUrl());
    }

    private function createTestSubject(): InitializeApplePayPaymentFactoryInterface
    {
        return new InitializeApplePayPaymentFactory();
    }
}

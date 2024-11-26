<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Exception\OrderCannotBeFoundException;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\Exception\PaymentCannotBeFoundException;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\InitializeApplePaySession;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\InitializeApplePaySessionHandler;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Request\InitializeApplePayPayment;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class InitializeApplePaySessionHandlerTest extends TestCase
{
    use ProphecyTrait;

    private OrderRepositoryInterface|ObjectProphecy $orderRepository;

    private PaymentRepositoryInterface|ObjectProphecy $paymentRepository;

    private Payum|ObjectProphecy $payum;

    private InitializeApplePayPaymentFactoryInterface|ObjectProphecy $initializeApplePayPaymentFactory;

    protected function setUp(): void
    {
        $this->orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->payum = $this->prophesize(Payum::class);
        $this->initializeApplePayPaymentFactory = $this->prophesize(InitializeApplePayPaymentFactoryInterface::class);
    }

    public function test_it_throws_an_exception_if_an_order_with_the_given_token_does_not_exist(): void
    {
        $this->expectException(OrderCannotBeFoundException::class);
        $this->expectExceptionMessage('Order with token "t0k3n" cannot be found.');

        $this->orderRepository->findOneByTokenValue('t0k3n')->willReturn(null);

        $this->createTestSubject()($this->createCommand());
    }

    public function test_it_throws_an_exception_if_a_given_payment_does_not_exist(): void
    {
        $this->expectException(PaymentCannotBeFoundException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $order = $this->prophesize(OrderInterface::class);

        $this->orderRepository->findOneByTokenValue('t0k3n')->willReturn($order);
        $this->paymentRepository->findOneBy(['id' => 1, 'order' => $order])->willReturn(null);

        $this->createTestSubject()($this->createCommand());
    }

    public function test_it_initializes_an_apple_pay_session(): void
    {
        $order = $this->prophesize(OrderInterface::class);

        $this->orderRepository->findOneByTokenValue('t0k3n')->willReturn($order);

        $payment = $this->createPayment();
        $payment->getDetails()->willReturn(['tpay' => ['apple_pay_session' => 'session']]);

        $this->paymentRepository->findOneBy(['id' => 1, 'order' => $order])->willReturn($payment);

        $this->initializeApplePayPaymentFactory->createNewWithModelAndOutput(
            $payment,
            'cw.nonexisting',
            'Commerce Weavers',
            'https://cw.nonexisting/validation',
        )->willReturn($request = $this->prophesize(InitializeApplePayPayment::class));

        $this->payum->getGateway('tpay_apple_pay')->willReturn($gateway = $this->prophesize(GatewayInterface::class));
        $gateway->execute($request)->shouldBeCalled();

        $result = $this->createTestSubject()($this->createCommand());

        $this->assertSame('session', $result->session);
    }

    private function createPayment(): PaymentInterface|ObjectProphecy
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay_apple_pay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);

        return $payment;
    }

    private function createCommand(): InitializeApplePaySession
    {
        return new InitializeApplePaySession(
            orderToken: 't0k3n',
            paymentId: 1,
            domainName: 'cw.nonexisting',
            displayName: 'Commerce Weavers',
            validationUrl: 'https://cw.nonexisting/validation',
        );
    }

    private function createTestSubject(): InitializeApplePaySessionHandler
    {
        return new InitializeApplePaySessionHandler(
            $this->orderRepository->reveal(),
            $this->paymentRepository->reveal(),
            $this->payum->reveal(),
            $this->initializeApplePayPaymentFactory->reveal(),
        );
    }
}

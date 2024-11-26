<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Refunding\Dispatcher;

use CommerceWeavers\SyliusTpayPlugin\Refunding\Checker\RefundPluginAvailabilityCheckerInterface;
use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcher;
use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcherInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use Payum\Core\Request\Refund;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundDispatcherTest extends TestCase
{
    use ProphecyTrait;

    private Payum|ObjectProphecy $payum;

    private RefundPluginAvailabilityCheckerInterface|ObjectProphecy $refundPluginAvailabilityChecker;

    protected function setUp(): void
    {
        $this->payum = $this->prophesize(Payum::class);
        $this->refundPluginAvailabilityChecker = $this->prophesize(RefundPluginAvailabilityCheckerInterface::class);
    }

    public function test_it_executes_a_refund_request_with_payment_if_plugin_is_not_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(false);
        $payment = $this->createPayment();

        $this->payum->getGateway('tpay')->willReturn($gateway = $this->prophesize(GatewayInterface::class));

        $gateway->execute(Argument::that(function (Refund $refund) use ($payment): bool {
            return $refund->getModel() === $payment->reveal();
        }))->shouldBeCalled();

        $this->createTestSubject()->dispatch($payment->reveal());
    }

    public function test_it_does_nothing_if_payment_is_passed_and_plugin_is_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(true);
        $payment = $this->createPayment();

        $this->payum->getGateway('tpay')->willReturn($gateway = $this->prophesize(GatewayInterface::class));

        $gateway->execute(Argument::any())->shouldNotBeCalled();

        $this->createTestSubject()->dispatch($payment->reveal());
    }

    public function test_it_executes_a_refund_request_with_refund_payment_if_plugin_is_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(true);
        $refundPayment = $this->createRefundPayment();

        $this->payum->getGateway('tpay')->willReturn($gateway = $this->prophesize(GatewayInterface::class));

        $gateway->execute(Argument::that(function (Refund $refund) use ($refundPayment): bool {
            return $refund->getModel() === $refundPayment->reveal();
        }))->shouldBeCalled();

        $this->createTestSubject()->dispatch($refundPayment->reveal());
    }

    public function test_it_does_nothing_if_refund_payment_is_passed_and_plugin_is_not_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(false);
        $refundPayment = $this->createRefundPayment();

        $this->payum->getGateway('tpay')->willReturn($gateway = $this->prophesize(GatewayInterface::class));

        $gateway->execute(Argument::any())->shouldNotBeCalled();

        $this->createTestSubject()->dispatch($refundPayment->reveal());
    }

    private function createPayment(): PaymentInterface|ObjectProphecy
    {
        $gatewayName = 'tpay';

        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn($gatewayName);

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);

        return $payment;
    }

    private function createRefundPayment(): RefundPaymentInterface|ObjectProphecy
    {
        $gatewayName = 'tpay';

        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn($gatewayName);

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $refundPayment = $this->prophesize(RefundPaymentInterface::class);
        $refundPayment->getPaymentMethod()->willReturn($paymentMethod);

        return $refundPayment;
    }

    private function createTestSubject(): RefundDispatcherInterface
    {
        return new RefundDispatcher(
            $this->payum->reveal(),
            $this->refundPluginAvailabilityChecker->reveal(),
        );
    }
}

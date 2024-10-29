<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Refunding\Dispatcher;

use CommerceWeavers\SyliusTpayPlugin\Refunding\Checker\RefundPluginAvailabilityCheckerInterface;
use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcher;
use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcherInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Refund;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundDispatcherTest extends TestCase
{
    use ProphecyTrait;

    private GatewayInterface|ObjectProphecy $gateway;

    private RefundPluginAvailabilityCheckerInterface|ObjectProphecy $refundPluginAvailabilityChecker;

    protected function setUp(): void
    {
        $this->gateway = $this->prophesize(GatewayInterface::class);
        $this->refundPluginAvailabilityChecker = $this->prophesize(RefundPluginAvailabilityCheckerInterface::class);
    }

    public function test_it_executes_a_refund_request_with_payment_if_plugin_is_not_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(false);
        $payment = $this->prophesize(PaymentInterface::class);

        $this->gateway->execute(Argument::that(function (Refund $refund) use ($payment): bool {
            return $refund->getModel() === $payment->reveal();
        }))->shouldBeCalled();

        $this->createTestSubject()->dispatch($payment->reveal());
    }

    public function test_it_does_nothing_if_payment_is_passed_and_plugin_is_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(true);
        $payment = $this->prophesize(PaymentInterface::class);

        $this->gateway->execute(Argument::any())->shouldNotBeCalled();

        $this->createTestSubject()->dispatch($payment->reveal());
    }

    public function test_it_executes_a_refund_request_with_refund_payment_if_plugin_is_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(true);
        $refundPayment = $this->prophesize(RefundPaymentInterface::class);

        $this->gateway->execute(Argument::that(function (Refund $refund) use ($refundPayment): bool {
            return $refund->getModel() === $refundPayment->reveal();
        }))->shouldBeCalled();

        $this->createTestSubject()->dispatch($refundPayment->reveal());
    }

    public function test_it_does_nothing_if_refund_payment_is_passed_and_plugin_is_not_available(): void
    {
        $this->refundPluginAvailabilityChecker->isAvailable()->willReturn(false);
        $refundPayment = $this->prophesize(RefundPaymentInterface::class);

        $this->gateway->execute(Argument::any())->shouldNotBeCalled();

        $this->createTestSubject()->dispatch($refundPayment->reveal());
    }

    private function createTestSubject(): RefundDispatcherInterface
    {
        return new RefundDispatcher(
            $this->gateway->reveal(),
            $this->refundPluginAvailabilityChecker->reveal(),
        );
    }
}

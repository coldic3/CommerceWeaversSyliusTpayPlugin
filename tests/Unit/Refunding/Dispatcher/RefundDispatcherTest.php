<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Refunding\Dispatcher;

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

final class RefundDispatcherTest extends TestCase
{
    use ProphecyTrait;

    private Payum|ObjectProphecy $payum;

    protected function setUp(): void
    {
        $this->payum = $this->prophesize(Payum::class);
    }

    public function test_it_executes_a_refund_request(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);

        $tpayGateway = $this->prophesize(GatewayInterface::class);
        $tpayGateway->execute(Argument::that(function (mixed $request) use ($payment) {
            return $request instanceof Refund && $request->getModel() === $payment->reveal();
        }))->shouldBeCalled();

        $this->payum->getGateway('tpay')->willReturn($tpayGateway);

        $this->createTestSubject()->dispatch($payment->reveal());
    }

    private function createTestSubject(): RefundDispatcherInterface
    {
        return new RefundDispatcher($this->payum->reveal());
    }
}

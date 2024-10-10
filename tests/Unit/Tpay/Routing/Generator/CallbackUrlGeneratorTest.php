<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Routing\Generator;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator\CallbackUrlGenerator;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator\CallbackUrlGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class CallbackUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    private RouterInterface|ObjectProphecy $router;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(RouterInterface::class);
    }

    public function test_it_generates_a_success_callback_url(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getTokenValue()->willReturn('order_token');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);

        $this->router
            ->generate(
                'success_route',
                ['_locale' => 'pl_PL', 'orderToken' => 'order_token'],
                UrlGeneratorInterface::ABSOLUTE_URL,
            )
            ->willReturn('https://cw.org/success')
        ;

        $callbackUrl = $this->createTestSubject()->generateSuccessUrl($payment->reveal(), 'pl_PL');

        $this->assertSame('https://cw.org/success', $callbackUrl);
    }

    public function test_it_uses_success_route_from_payment_details_if_present(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getTokenValue()->willReturn('order_token');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([
            'tpay' => [
                'success_url' => 'https://cw.org/success',
            ],
        ]);

        $callbackUrl = $this->createTestSubject()->generateSuccessUrl($payment->reveal(), 'pl_PL');

        $this->assertSame('https://cw.org/success', $callbackUrl);
    }

    public function test_it_generates_a_failure_callback_url(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getTokenValue()->willReturn('order_token');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);

        $this->router
            ->generate(
                'failure_route',
                ['_locale' => 'pl_PL', 'orderToken' => 'order_token'],
                UrlGeneratorInterface::ABSOLUTE_URL,
            )
            ->willReturn('https://cw.org/failure')
        ;

        $callbackUrl = $this->createTestSubject()->generateFailureUrl($payment->reveal(), 'pl_PL');

        $this->assertSame('https://cw.org/failure', $callbackUrl);
    }

    public function test_it_uses_failure_route_from_payment_details_if_present(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getTokenValue()->willReturn('order_token');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([
            'tpay' => [
                'failure_url' => 'https://cw.org/failure',
            ],
        ]);

        $callbackUrl = $this->createTestSubject()->generateFailureUrl($payment->reveal(), 'pl_PL');

        $this->assertSame('https://cw.org/failure', $callbackUrl);
    }

    private function createTestSubject(): CallbackUrlGeneratorInterface
    {
        return new CallbackUrlGenerator($this->router->reveal(), 'success_route', 'failure_route');
    }
}

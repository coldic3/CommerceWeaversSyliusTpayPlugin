<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\InvalidArgumentException;

final class CreateRedirectBasedPaymentPayloadFactoryTest extends TestCase
{
    use ProphecyTrait;

    private RouterInterface|ObjectProphecy $router;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(RouterInterface::class);
    }

    public function test_it_returns_a_payload_for_a_redirect_based_payment(): void
    {
        $billingAddress = $this->prophesize(AddressInterface::class);
        $billingAddress->getFullName()->willReturn('Don Matteo');

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('don.matteo@sandomierz.org');

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getNumber()->willReturn('000000001');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getAmount()->willReturn(1050);

        $this->router
            ->generate('cw_success', ['_locale' => 'pl_PL'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://cw.org/success')
        ;
        $this->router
            ->generate('cw_error', ['_locale' => 'pl_PL'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://cw.org/error')
        ;

        $payload = $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');

        $this->assertSame([
            'amount' => '10.50',
            'description' => 'zamówienie #000000001',
            'payer' => [
                'email' => 'don.matteo@sandomierz.org',
                'name' => 'Don Matteo',
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => 'https://cw.org/success',
                    'error' => 'https://cw.org/error',
                ],
                'notification' => [
                    'url' => 'https://cw.org/notify',
                ],
            ],
        ], $payload);
    }

    public function test_it_throws_an_exception_if_the_order_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $payment = $this->prophesize(PaymentInterface::class);

        $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');
    }


    public function test_it_throws_an_exception_if_the_customer_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn(null);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);

        $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');
    }

    public function test_it_throws_an_exception_if_the_billing_address_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $customer = $this->prophesize(CustomerInterface::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn(null);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);

        $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');
    }

    public function test_it_throws_an_exception_if_the_amount_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $billingAddress = $this->prophesize(AddressInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn($billingAddress);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getAmount()->willReturn(null);

        $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');
    }

    private function createTestSubject(): CreateRedirectBasedPaymentPayloadFactoryInterface
    {
        return new CreateRedirectBasedPaymentPayloadFactory(
            $this->router->reveal(),
            'cw_success',
            'cw_error',
        );
    }
}

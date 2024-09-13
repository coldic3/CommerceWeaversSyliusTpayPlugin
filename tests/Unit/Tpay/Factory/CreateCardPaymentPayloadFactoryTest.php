<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateCardPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateCardPaymentPayloadFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;

final class CreateCardPaymentPayloadFactoryTest extends TestCase
{
    use ProphecyTrait;

    private CreateRedirectBasedPaymentPayloadFactoryInterface|ObjectProphecy $createRedirectBasedPaymentPayloadFactory;

    protected function setUp(): void
    {
        $this->createRedirectBasedPaymentPayloadFactory = $this->prophesize(CreateRedirectBasedPaymentPayloadFactoryInterface::class);
    }

    public function test_it_adds_card_related_data_to_a_basic_create_payment_payload_output(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);

        $this->createRedirectBasedPaymentPayloadFactory->createFrom($payment, 'https://cw.org/notify', 'pl_PL')->willReturn(['some' => 'data']);

        $payload = $this->createTestSubject()->createFrom($payment->reveal(), 'https://cw.org/notify', 'pl_PL');

        $this->assertSame([
            'some' => 'data',
            'pay' => [
                'groupId' => 103,
            ],
        ], $payload);
    }

    private function createTestSubject(): CreateCardPaymentPayloadFactoryInterface
    {
        return new CreateCardPaymentPayloadFactory($this->createRedirectBasedPaymentPayloadFactory->reveal());
    }
}

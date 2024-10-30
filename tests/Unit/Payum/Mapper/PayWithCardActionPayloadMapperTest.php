<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Mapper\PayWithCardActionPayloadMapper;
use CommerceWeavers\SyliusTpayPlugin\Repository\CreditCardRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class PayWithCardActionPayloadMapperTest extends TestCase
{
    use ProphecyTrait;

    private CreditCardRepositoryInterface|ObjectProphecy $creditCardRepository;

    protected function setUp(): void
    {
        $this->creditCardRepository = $this->prophesize(CreditCardRepositoryInterface::class);
    }

    public function test_it_returns_payload_for_card_payment(): void
    {
        $paymentDetails = $this->prophesize(PaymentDetails::class);

        $paymentDetails->getUseSavedCreditCard()->willReturn(null);
        $paymentDetails->getEncodedCardData()->willReturn('encoded_card_data');
        $paymentDetails->isSaveCreditCardForLater()->willReturn(false);

        $mapper = $this->createTestSubject();
        $payload = $mapper->getPayload($paymentDetails->reveal());

        $this->assertSame([
            'groupId' => PayGroup::CARD,
            'cardPaymentData' => [
                'card' => 'encoded_card_data',
            ],
        ], $payload);
    }

    public function test_it_returns_payload_for_saved_card_payment(): void
    {
        $paymentDetails = $this->prophesize(PaymentDetails::class);

        $paymentDetails->getUseSavedCreditCard()->willReturn(1);

        $creditCard = $this->prophesize(CreditCardInterface::class);
        $creditCard->getToken()->willReturn('token');

        $this->creditCardRepository->find(1)->willReturn($creditCard->reveal());

        $mapper = $this->createTestSubject();
        $payload = $mapper->getPayload($paymentDetails->reveal());

        $this->assertSame([
            'groupId' => PayGroup::CARD,
            'cardPaymentData' => [
                'token' => 'token',
            ],
        ], $payload);
    }

    public function test_it_returns_payload_for_card_payment_and_save_for_later(): void
    {
        $paymentDetails = $this->prophesize(PaymentDetails::class);

        $paymentDetails->getUseSavedCreditCard()->willReturn(null);
        $paymentDetails->isSaveCreditCardForLater()->willReturn(true);
        $paymentDetails->getEncodedCardData()->willReturn('encoded_card_data');

        $mapper = $this->createTestSubject();
        $payload = $mapper->getPayload($paymentDetails->reveal());

        $this->assertSame([
            'groupId' => PayGroup::CARD,
            'cardPaymentData' => [
                'card' => 'encoded_card_data',
                'save' => true,
            ],
        ], $payload);
    }

    private function createTestSubject(): PayWithCardActionPayloadMapper
    {
        return new PayWithCardActionPayloadMapper(
            $this->creditCardRepository->reveal()
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Factory\NextCommand;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayBySavedCard;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommand\PayBySavedCardFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;

final class PayBySavedCardFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_does_not_support_a_command_without_an_saved_card_data(): void
    {
        $factory = $this->createTestSubject();

        $this->assertFalse($factory->supports($this->createCommand(), $this->createPayment()));
    }

    public function test_it_does_not_support_a_command_without_a_payment_with_id(): void
    {
        $factory = $this->createTestSubject();

        $this->assertFalse($factory->supports($this->createCommand(savedCardId: 'e0f79275-18ef-4edf-b8fc-adc40fdcbcc0'), new Payment()));
    }

    public function test_it_supports_a_command_with_an_saved_card_data(): void
    {
        $factory = $this->createTestSubject();

        $this->assertTrue($factory->supports($this->createCommand(savedCardId: 'e0f79275-18ef-4edf-b8fc-adc40fdcbcc0'), $this->createPayment()));
    }

    public function test_it_creates_a_pay_by_saved_card_command(): void
    {
        $command = $this->createTestSubject()->create($this->createCommand(savedCardId: 'e0f79275-18ef-4edf-b8fc-adc40fdcbcc0'), $this->createPayment());

        $this->assertInstanceOf(PayBySavedCard::class, $command);
        $this->assertSame('e0f79275-18ef-4edf-b8fc-adc40fdcbcc0', $command->savedCardId);
    }

    public function test_it_throws_an_exception_when_trying_to_create_a_command_with_unsupported_factory(): void
    {
        $this->expectException(UnsupportedNextCommandFactory::class);

        $this->createTestSubject()->create($this->createCommand(), new Payment());
    }

    private function createCommand(?string $token = null, ?string $savedCardId = null, bool $saveCard = false): Pay
    {
        return new Pay(
            $token ?? 'token',
            'https://cw.nonexisting/success',
            'https://cw.nonexisting/failure',
            savedCardId: $savedCardId,
            saveCard: $saveCard,
        );
    }

    private function createPayment(int $id = 1): PaymentInterface
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getId()->willReturn($id);

        return $payment->reveal();
    }

    private function createTestSubject(): NextCommandFactoryInterface
    {
        return new PayBySavedCardFactory();
    }
}

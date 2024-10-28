<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Factory\NextCommand;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByCard;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommand\PayByCardFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;

final class PayByCardFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_does_not_support_a_command_without_an_encoded_card_data(): void
    {
        $factory = $this->createTestSubject();

        $this->assertFalse($factory->supports($this->createCommand(), $this->createPayment()));
    }

    public function test_it_does_not_support_a_command_without_a_payment_with_id(): void
    {
        $factory = $this->createTestSubject();

        $this->assertFalse($factory->supports($this->createCommand(encodedCardData: 'card_data'), new Payment()));
    }

    public function test_it_supports_a_command_with_an_encoded_card_data(): void
    {
        $factory = $this->createTestSubject();

        $this->assertTrue($factory->supports($this->createCommand(encodedCardData: 'card_data'), $this->createPayment()));
    }

    public function test_it_creates_a_pay_by_card_command(): void
    {
        $command = $this->createTestSubject()->create($this->createCommand(encodedCardData: 'card_data'), $this->createPayment());

        $this->assertInstanceOf(PayByCard::class, $command);
        $this->assertSame('card_data', $command->encodedCardData);
    }

    public function test_it_creates_a_pay_by_card_command_with_save_card_information(): void
    {
        $command = $this->createTestSubject()->create($this->createCommand(encodedCardData: 'card_data', saveCard: true), $this->createPayment());

        $this->assertInstanceOf(PayByCard::class, $command);
        $this->assertSame('card_data', $command->encodedCardData);
        $this->assertSame(true, $command->saveCard);
    }

    public function test_it_throws_an_exception_when_trying_to_create_a_command_with_unsupported_factory(): void
    {
        $this->expectException(UnsupportedNextCommandFactory::class);

        $this->createTestSubject()->create($this->createCommand(), new Payment());
    }

    private function createCommand(?string $token = null, ?string $encodedCardData = null, bool $saveCard = false): Pay
    {
        return new Pay(
            $token ?? 'token',
            'https://cw.nonexisting/success',
            'https://cw.nonexisting/failure',
            encodedCardData: $encodedCardData,
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
        return new PayByCardFactory();
    }
}

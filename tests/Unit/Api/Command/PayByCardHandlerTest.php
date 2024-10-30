<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByCard;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByCardHandler;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use Payum\Core\Payum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\CommerceWeavers\SyliusTpayPlugin\Helper\PaymentDetailsHelperTrait;
use function Symfony\Component\Translation\t;

final class PayByCardHandlerTest extends TestCase
{
    use ProphecyTrait;

    use PaymentDetailsHelperTrait;

    private PaymentRepositoryInterface|ObjectProphecy $paymentRepository;

    private Payum|ObjectProphecy $payum;

    private CreateTransactionProcessorInterface|ObjectProphecy $createTransactionProcessor;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->createTransactionProcessor = $this->prophesize(CreateTransactionProcessorInterface::class);
    }

    public function test_it_throw_an_exception_if_a_payment_cannot_be_found(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $this->paymentRepository->find(1)->willReturn(null);

        $this->createTestSubject()->__invoke(new PayByCard(1, 'encoded_card_data'));
    }

    public function test_it_creates_a_card_based_transaction(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'pending', 'payment_url' => 'https://cw.org/pay']]);
        $payment->setDetails(
            $this->getExpectedDetails(card: 'encoded_card_data'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByCard(1, 'encoded_card_data'));

        $this->assertSame('pending', $result->status);
        $this->assertSame('https://cw.org/pay', $result->transactionPaymentUrl);
    }

    public function test_it_creates_a_card_based_transaction_with_saving_card_option(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'pending', 'payment_url' => 'https://cw.org/pay']]);
        $payment->setDetails(
            $this->getExpectedDetails(card: 'encoded_card_data', save_credit_card_for_later: true),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByCard(1, 'encoded_card_data', true));

        $this->assertSame('pending', $result->status);
        $this->assertSame('https://cw.org/pay', $result->transactionPaymentUrl);
    }

    private function createTestSubject(): PayByCardHandler
    {
        return new PayByCardHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
        );
    }
}

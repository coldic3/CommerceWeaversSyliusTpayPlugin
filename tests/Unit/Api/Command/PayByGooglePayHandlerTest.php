<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByGooglePay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByGooglePayHandler;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use Payum\Core\Payum;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\CommerceWeavers\SyliusTpayPlugin\Helper\PaymentDetailsHelperTrait;
use Webmozart\Assert\InvalidArgumentException;

final class PayByGooglePayHandlerTest extends TestCase
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
        $this->paymentRepository->find(1)->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $this->createTestSubject()->__invoke(new PayByGooglePay(1, 't00k33n'));
    }

    public function test_it_throws_an_exception_if_a_payment_status_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment status is required to create a result.');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => []]);
        $this->paymentRepository->find(1)->willReturn($payment);

        $payment->setDetails(
            $this->getExpectedDetails(google_pay_token: 't00k33n')
        )->shouldBeCalled();

        $this->createTestSubject()->__invoke(new PayByGooglePay(1, 't00k33n'));
    }

    public function test_it_creates_a_google_pay_based_transaction(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'pending']]);
        $payment->setDetails(
            $this->getExpectedDetails(google_pay_token: 't00k33n'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByGooglePay(1, 't00k33n'));

        $this->assertSame('pending', $result->status);
    }

    private function createTestSubject(): PayByGooglePayHandler
    {
        return new PayByGooglePayHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
        );
    }
}

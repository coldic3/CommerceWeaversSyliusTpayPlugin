<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlik;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlikHandler;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\CommerceWeavers\SyliusTpayPlugin\Helper\PaymentDetailsHelperTrait;

final class PayByBlikHandlerTest extends TestCase
{
    use ProphecyTrait;

    use PaymentDetailsHelperTrait;

    private PaymentRepositoryInterface|ObjectProphecy $paymentRepository;

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

        $this->createTestSubject()->__invoke(new PayByBlik(1, '777123'));
    }

    public function test_it_creates_a_blik_based_transaction(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123'));

        self::assertSame('success', $result->status);
    }

    private function createTestSubject(): PayByBlikHandler
    {
        return new PayByBlikHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
        );
    }
}

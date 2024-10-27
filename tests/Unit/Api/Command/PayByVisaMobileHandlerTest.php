<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByVisaMobile;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByVisaMobileHandler;
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

final class PayByVisaMobileHandlerTest extends TestCase
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

        $this->createTestSubject()->__invoke(new PayByVisaMobile(1, visaMobilePhoneNumber: '44123456789'));
    }

    public function test_it_throws_an_exception_if_payment_details_does_not_have_a_set_status(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment status is required to create a result.');

        $paymentDetails = ['tpay' => ['status' => null]];

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn($paymentDetails);
        $payment->setDetails(
            $this->getExpectedDetails(visa_mobile_phone_number: '44123456789'),
        );

        $this->paymentRepository->find(1)->willReturn($payment);

        $this->createTestSubject()->__invoke(new PayByVisaMobile(1, visaMobilePhoneNumber: '44123456789'));
    }

    public function test_it_throws_validation_error_if_phone_number_is_incorrect(): void
    {
        $paymentDetails = ['tpay' => ['status' => 'pending', 'payment_url' => 'https://cw.org/pay', 'visa_mobile_phone_number' => '23456789']];

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn($paymentDetails);
        $payment->setDetails(
            $this->getExpectedDetails(status: 'pending', payment_url: 'https://cw.org/pay', visa_mobile_phone_number: '44123456789'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByVisaMobile(1, visaMobilePhoneNumber: '44123456789'));

        $this->assertSame('pending', $result->status);
        $this->assertSame('https://cw.org/pay', $result->transactionPaymentUrl);
    }

    public function test_it_creates_a_visa_mobile_based_transaction(): void
    {
        $paymentDetails = ['tpay' => ['status' => 'pending', 'payment_url' => 'https://cw.org/pay', 'visa_mobile_phone_number' => '44123456789']];

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn($paymentDetails);
        $payment->setDetails(
            $this->getExpectedDetails(status: 'pending', payment_url: 'https://cw.org/pay', visa_mobile_phone_number: '44123456789'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $result = $this->createTestSubject()->__invoke(new PayByVisaMobile(1, visaMobilePhoneNumber: '44123456789'));

        $this->assertSame('pending', $result->status);
        $this->assertSame('https://cw.org/pay', $result->transactionPaymentUrl);
    }

    private function createTestSubject(): PayByVisaMobileHandler
    {
        return new PayByVisaMobileHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
        );
    }
}

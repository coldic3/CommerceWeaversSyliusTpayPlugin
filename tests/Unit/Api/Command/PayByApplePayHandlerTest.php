<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByApplePay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByApplePayHandler;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\CreateTransactionFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\CommerceWeavers\SyliusTpayPlugin\Helper\PaymentDetailsHelperTrait;
use Webmozart\Assert\InvalidArgumentException;

final class PayByApplePayHandlerTest extends TestCase
{
    use ProphecyTrait;

    use PaymentDetailsHelperTrait;

    private PaymentRepositoryInterface|ObjectProphecy $paymentRepository;

    private Payum|ObjectProphecy $payum;

    private CreateTransactionFactoryInterface|ObjectProphecy $createTransactionFactory;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->payum = $this->prophesize(Payum::class);
        $this->createTransactionFactory = $this->prophesize(CreateTransactionFactoryInterface::class);
    }

    public function test_it_throw_an_exception_if_a_payment_cannot_be_found(): void
    {
        $this->paymentRepository->find(1)->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $this->createTestSubject()->__invoke(new PayByApplePay(1, 't00k33n'));
    }

    public function test_it_throws_an_exception_if_a_gateway_name_cannot_be_determined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Gateway name cannot be determined.');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([]);
        $payment->getMethod()->willReturn(null);
        $payment->setDetails(Argument::any())->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $this->createTestSubject()->__invoke(new PayByApplePay(1, 't00k33n'));
    }

    public function test_it_throws_an_exception_if_a_payment_status_is_null(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => []]);

        $createTransaction = $this->prophesize(CreateTransaction::class);
        $gateway = $this->prophesize(GatewayInterface::class);

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);
        $this->payum->getGateway('tpay')->willReturn($gateway);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment status is required to create a result.');
        $payment->setDetails(
            $this->getExpectedDetails(apple_pay_token: 't00k33n')
        )->shouldBeCalled();
        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();

        $this->createTestSubject()->__invoke(new PayByApplePay(1, 't00k33n'));
    }

    public function test_it_creates_a_apple_pay_based_transaction(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'pending']]);

        $createTransaction = $this->prophesize(CreateTransaction::class);
        $gateway = $this->prophesize(GatewayInterface::class);

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);
        $this->payum->getGateway('tpay')->willReturn($gateway);

        $result = $this->createTestSubject()->__invoke(new PayByApplePay(1, 't00k33n'));

        self::assertSame('pending', $result->status);
        $payment->setDetails(
            $this->getExpectedDetails(apple_pay_token: 't00k33n')
        )->shouldBeCalled();

        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();
    }

    private function createTestSubject(): PayByApplePayHandler
    {
        return new PayByApplePayHandler(
            $this->paymentRepository->reveal(),
            $this->payum->reveal(),
            $this->createTransactionFactory->reveal(),
        );
    }
}

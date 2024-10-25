<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlik;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlikHandler;
use CommerceWeavers\SyliusTpayPlugin\Api\Exception\BlikAliasAmbiguousValueException as ApiBlikAliasAmbiguousValueException;
use CommerceWeavers\SyliusTpayPlugin\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\BlikAliasAmbiguousValueException as PayumBlikAliasAmbiguousValueException;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Resolver\BlikAliasResolverInterface;
use Doctrine\Persistence\ObjectManager;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\GatewayConfigInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
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

    private BlikAliasResolverInterface|ObjectProphecy $blikAliasResolver;

    private ObjectManager|ObjectProphecy $blikAliasManager;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->createTransactionProcessor = $this->prophesize(CreateTransactionProcessorInterface::class);
        $this->blikAliasResolver = $this->prophesize(BlikAliasResolverInterface::class);
        $this->blikAliasManager = $this->prophesize(ObjectManager::class);
    }

    public function test_it_throw_an_exception_if_a_payment_cannot_be_found(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $this->paymentRepository->find(1)->willReturn(null);

        $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null, true));
    }

    public function test_it_throws_an_exception_if_a_gateway_name_cannot_be_determined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Gateway name cannot be determined.');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([]);
        $payment->setDetails(Argument::any());
        $payment->getMethod()->willReturn(null);

        $this->paymentRepository->find(1)->willReturn($payment);

        $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null, false));
    }

    // fixme after resolving conflicts
    public function test_it_throws_blik_alias_ambiguous_value_exception_if_blik_alias_value_is_ambiguous(): void
    {
        $this->expectException(ApiBlikAliasAmbiguousValueException::class);

        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails([
            'tpay' => [
                'transaction_id' => null,
                'result' => null,
                'status' => null,
                'apple_pay_token' => null,
                'blik_token' => '777123',
                'blik_alias_value' => null,
                'blik_alias_application_code' => null,
                'google_pay_token' => null,
                'card' => null,
                'payment_url' => null,
                'success_url' => null,
                'failure_url' => null,
                'tpay_channel_id' => null,
                'visa_mobile_phone_number' => null,
            ],
        ])->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $createTransaction = $this->prophesize(CreateTransaction::class);

        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);

        $gateway = $this->prophesize(GatewayInterface::class);
        $gateway->execute($createTransaction, catchReply: true)->willThrow(PayumBlikAliasAmbiguousValueException::class);

        $this->payum->getGateway('tpay')->willReturn($gateway);

        $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null));
    }

    // fixme after resolving conflicts
    public function test_it_creates_a_blik_based_transaction_with_blik_token_only(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123', blik_save_alias: true),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $createTransaction = $this->prophesize(CreateTransaction::class);

        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);

        $gateway = $this->prophesize(GatewayInterface::class);
        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();

        $this->payum->getGateway('tpay')->willReturn($gateway);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null));

        self::assertSame('success', $result->status);
    }

    // fixme after resolving conflicts
    public function test_it_creates_a_blik_based_transaction_saving_blik_alias(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $customer = $this->prophesize(CustomerInterface::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->getOrder()->willReturn($order);
        $payment->setDetails([
            'tpay' => [
                'transaction_id' => null,
                'result' => null,
                'status' => null,
                'apple_pay_token' => null,
                'blik_token' => '777123',
                'blik_alias_value' => 'iamablikalias',
                'blik_alias_application_code' => null,
                'google_pay_token' => null,
                'card' => null,
                'payment_url' => null,
                'success_url' => null,
                'failure_url' => null,
                'tpay_channel_id' => null,
                'visa_mobile_phone_number' => null,
            ],
        ])->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldBeCalled();

        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);

        $createTransaction = $this->prophesize(CreateTransaction::class);

        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);

        $gateway = $this->prophesize(GatewayInterface::class);
        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();

        $this->payum->getGateway('tpay')->willReturn($gateway);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null, true));

        self::assertSame('success', $result->status);
    }

    // fixme after resolving conflicts
    public function test_it_creates_a_blik_based_transaction_using_blik_alias(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $customer = $this->prophesize(CustomerInterface::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->getOrder()->willReturn($order);
        $payment->setDetails([
            'tpay' => [
                'transaction_id' => null,
                'result' => null,
                'status' => null,
                'apple_pay_token' => null,
                'blik_token' => null,
                'blik_alias_value' => 'iamablikalias',
                'blik_alias_application_code' => null,
                'google_pay_token' => null,
                'card' => null,
                'payment_url' => null,
                'success_url' => null,
                'failure_url' => null,
                'tpay_channel_id' => null,
                'visa_mobile_phone_number' => null,
            ],
        ])->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldNotBeCalled();

        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);

        $createTransaction = $this->prophesize(CreateTransaction::class);

        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);

        $gateway = $this->prophesize(GatewayInterface::class);
        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();

        $this->payum->getGateway('tpay')->willReturn($gateway);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, null, null, false, true));

        self::assertSame('success', $result->status);
    }

    public function test_it_creates_a_blik_based_transaction_using_blik_alias_with_application_code(): void
    {
        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $customer = $this->prophesize(CustomerInterface::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getCustomer()->willReturn($customer);

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getMethod()->willReturn($paymentMethod);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->getOrder()->willReturn($order);
        $payment->setDetails([
            'tpay' => [
                'transaction_id' => null,
                'result' => null,
                'status' => null,
                'apple_pay_token' => null,
                'blik_token' => null,
                'blik_alias_value' => 'iamablikalias',
                'blik_alias_application_code' => 'iamablikaliasapplicationcode',
                'google_pay_token' => null,
                'card' => null,
                'payment_url' => null,
                'success_url' => null,
                'failure_url' => null,
                'tpay_channel_id' => null,
                'visa_mobile_phone_number' => null,
            ],
        ])->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldNotBeCalled();

        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);

        $createTransaction = $this->prophesize(CreateTransaction::class);

        $this->createTransactionFactory->createNewWithModel($payment)->willReturn($createTransaction);

        $gateway = $this->prophesize(GatewayInterface::class);
        $gateway->execute($createTransaction, catchReply: true)->shouldBeCalled();

        $this->payum->getGateway('tpay')->willReturn($gateway);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, null, 'iamablikaliasapplicationcode', false, true));

        self::assertSame('success', $result->status);
    }

    private function createTestSubject(): PayByBlikHandler
    {
        return new PayByBlikHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
            $this->blikAliasResolver->reveal(),
            $this->blikAliasManager->reveal(),
        );
    }
}

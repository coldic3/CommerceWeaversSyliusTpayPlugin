<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Command;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlik;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlikHandler;
use CommerceWeavers\SyliusTpayPlugin\Api\Enum\BlikAliasAction;
use CommerceWeavers\SyliusTpayPlugin\Api\Exception\BlikAliasAmbiguousValueException as ApiBlikAliasAmbiguousValueException;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\PreconditionGuard\ActiveBlikAliasPreconditionGuardInterface;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Resolver\BlikAliasResolverInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\BlikAliasAmbiguousValueException as PayumBlikAliasAmbiguousValueException;
use CommerceWeavers\SyliusTpayPlugin\Payum\Processor\CreateTransactionProcessorInterface;
use Doctrine\Persistence\ObjectManager;
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

    private ActiveBlikAliasPreconditionGuardInterface|ObjectProphecy $activeBlikAliasPreconditionGuard;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->prophesize(PaymentRepositoryInterface::class);
        $this->createTransactionProcessor = $this->prophesize(CreateTransactionProcessorInterface::class);
        $this->blikAliasResolver = $this->prophesize(BlikAliasResolverInterface::class);
        $this->blikAliasManager = $this->prophesize(ObjectManager::class);
        $this->activeBlikAliasPreconditionGuard = $this->prophesize(ActiveBlikAliasPreconditionGuardInterface::class);
    }

    public function test_it_throw_an_exception_if_a_payment_cannot_be_found(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Payment with id "1" cannot be found.');

        $this->paymentRepository->find(1)->willReturn(null);

        $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null, null));
    }

    public function test_it_creates_a_blik_based_transaction_with_blik_token_only(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123'),
        )->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);

        $this->createTransactionProcessor->process($payment)->shouldBeCalled();

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', null, null));

        self::assertSame('success', $result->status);
    }

    public function test_it_creates_a_blik_based_transaction_saving_blik_alias(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123', blik_alias_value: 'iamablikalias'),
        )->shouldBeCalled();
        $payment->getOrder()->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);
        $this->createTransactionProcessor->process($payment)->shouldBeCalled();

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', BlikAliasAction::REGISTER, null));

        self::assertSame('success', $result->status);
    }

    public function test_it_creates_a_blik_based_transaction_using_blik_alias(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123', blik_alias_value: 'iamablikalias'),
        )->shouldBeCalled();
        $payment->getOrder()->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldNotBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);
        $this->activeBlikAliasPreconditionGuard->denyIfNotActive($blikAlias)->shouldBeCalled();
        $this->createTransactionProcessor->process($payment)->shouldBeCalled();

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', BlikAliasAction::APPLY, null));

        self::assertSame('success', $result->status);
    }

    public function test_it_creates_a_blik_based_transaction_using_blik_alias_with_application_code(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(
                blik_token: '777123',
                blik_alias_value: 'iamablikalias',
                blik_alias_application_code: 'iamablikaliasapplicationcode',
            ),
        )->shouldBeCalled();
        $payment->getOrder()->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldNotBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);
        $this->activeBlikAliasPreconditionGuard->denyIfNotActive($blikAlias)->shouldBeCalled();
        $this->createTransactionProcessor->process($payment)->shouldBeCalled();

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', BlikAliasAction::APPLY, 'iamablikaliasapplicationcode'));

        self::assertSame('success', $result->status);
    }

    public function test_it_throws_blik_alias_ambiguous_value_exception_if_blik_alias_value_is_ambiguous(): void
    {
        $this->expectException(ApiBlikAliasAmbiguousValueException::class);

        $payment = $this->prophesize(PaymentInterface::class);
        $order = $this->prophesize(OrderInterface::class);
        $customer = $this->prophesize(CustomerInterface::class);
        $blikAlias = $this->prophesize(BlikAliasInterface::class);
        $payment->getDetails()->willReturn([], ['tpay' => ['status' => 'success']]);
        $payment->setDetails(
            $this->getExpectedDetails(blik_token: '777123', blik_alias_value: 'iamablikalias'),
        )->shouldBeCalled();
        $payment->getOrder()->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $blikAlias->getValue()->willReturn('iamablikalias');
        $blikAlias->redefine()->shouldNotBeCalled();

        $this->paymentRepository->find(1)->willReturn($payment);
        $this->blikAliasResolver->resolve($customer)->willReturn($blikAlias);
        $this->activeBlikAliasPreconditionGuard->denyIfNotActive($blikAlias)->shouldBeCalled();
        $this->createTransactionProcessor->process($payment)->willThrow(PayumBlikAliasAmbiguousValueException::class);

        $result = $this->createTestSubject()->__invoke(new PayByBlik(1, '777123', BlikAliasAction::APPLY, null));

        self::assertSame('success', $result->status);
    }

    private function createTestSubject(): PayByBlikHandler
    {
        return new PayByBlikHandler(
            $this->paymentRepository->reveal(),
            $this->createTransactionProcessor->reveal(),
            $this->blikAliasResolver->reveal(),
            $this->blikAliasManager->reveal(),
            $this->activeBlikAliasPreconditionGuard->reveal(),
        );
    }
}

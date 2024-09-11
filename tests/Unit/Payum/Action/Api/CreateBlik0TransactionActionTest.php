<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateBlik0TransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateBlik0PaymentPayloadFactoryInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Api\Transactions\TransactionsApi;

final class CreateBlik0TransactionActionTest extends TestCase
{
    use ProphecyTrait;

    private TpayApi|ObjectProphecy $api;

    private CreateBlik0PaymentPayloadFactoryInterface|ObjectProphecy $createBlik0PaymentPayloadFactory;

    private NotifyTokenFactoryInterface|ObjectProphecy $notifyTokenFactory;

    private GatewayInterface|ObjectProphecy $gateway;

    protected function setUp(): void
    {
        $this->api = $this->prophesize(TpayApi::class);
        $this->createBlik0PaymentPayloadFactory = $this->prophesize(CreateBlik0PaymentPayloadFactoryInterface::class);
        $this->notifyTokenFactory = $this->prophesize(NotifyTokenFactoryInterface::class);
        $this->gateway = $this->prophesize(GatewayInterface::class);
    }

    public function test_it_supports_create_transaction_requests_with_a_valid_payment_model(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn(['tpay' => ['blik_token' => '123456']]);

        $request = $this->prophesize(CreateTransaction::class);
        $request->getModel()->willReturn($payment);

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertTrue($isSupported);
    }

    public function test_it_does_not_support_non_create_transaction_requests(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn([]);

        $request = $this->prophesize(Capture::class);
        $request->getModel()->willReturn($payment);

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertFalse($isSupported);
    }

    public function test_it_does_not_support_requests_with_non_payment_model(): void
    {
        $nonPaymentModel = new \stdClass();

        $request = $this->prophesize(CreateTransaction::class);
        $request->getModel()->willReturn($nonPaymentModel);

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertFalse($isSupported);
    }

    public function test_it_does_not_support_requests_with_payment_model_not_containing_tpay_blik(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getDetails()->willReturn(['tpay' => ['card' => 'some_crazy_card_hash']]);

        $request = $this->prophesize(CreateTransaction::class);
        $request->getModel()->willReturn($payment);

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertFalse($isSupported);
    }

    public function test_it_creates_a_payment_and_requests_paying_it_with_a_provided_blik_token(): void
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getLocaleCode()->willReturn('pl_PL');

        $payment = $this->prophesize(PaymentInterface::class);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);

        $token = $this->prophesize(TokenInterface::class);
        $token->getGatewayName()->willReturn('tpay');

        $request = $this->prophesize(CreateTransaction::class);
        $request->getModel()->willReturn($payment);
        $request->getToken()->willReturn($token);

        $notifyToken = $this->prophesize(TokenInterface::class);
        $notifyToken->getTargetUrl()->willReturn('https://cw.org/notify');

        $transactions = $this->prophesize(TransactionsApi::class);
        $transactions->createTransaction(['factored' => 'payload'])->willReturn([
            'transactionId' => 'tr4ns4ct!0n_id',
            'status' => 'correct',
        ]);

        $this->api->transactions()->willReturn($transactions);

        $payment->setDetails([
            'tpay' => [
                'transaction_id' => 'tr4ns4ct!0n_id',
                'status' => 'correct',
            ],
        ])->shouldBeCalled();

        $this->notifyTokenFactory->create($payment, 'tpay', 'pl_PL')->willReturn($notifyToken);

        $this->createBlik0PaymentPayloadFactory
            ->createFrom($payment, 'https://cw.org/notify', 'pl_PL')
            ->willReturn(['factored' => 'payload'])
        ;

        $this->createTestSubject()->execute($request->reveal());
    }

    private function createTestSubject(): CreateBlik0TransactionAction
    {
        $action = new CreateBlik0TransactionAction(
            $this->createBlik0PaymentPayloadFactory->reveal(),
            $this->notifyTokenFactory->reveal(),
        );

        $action->setApi($this->api->reveal());

        return $action;
    }
}

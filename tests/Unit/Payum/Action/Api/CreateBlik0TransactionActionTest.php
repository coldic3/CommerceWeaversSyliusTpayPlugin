<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateBlik0TransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateBlik0Transaction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Request\Sync;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Api\Transactions\TransactionsApi;

final class CreateBlik0TransactionActionTest extends TestCase
{
    use ProphecyTrait;

    private CreateBlik0Transaction|ObjectProphecy $request;
    private PaymentInterface|ObjectProphecy $model;
    private TpayApi|ObjectProphecy $api;
    private RouterInterface|ObjectProphecy $router;
    private GenericTokenFactoryInterface|ObjectProphecy $tokenFactory;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(CreateTransaction::class);
        $this->model = $this->prophesize(PaymentInterface::class);
        $this->api = $this->prophesize(TpayApi::class);
        $this->router = $this->prophesize(RouterInterface::class);
        $this->tokenFactory = $this->prophesize(GenericTokenFactoryInterface::class);

        $this->request->getModel()->willReturn($this->model->reveal());
    }

    public function test_it_supports_only_create_blik0_transaction_request(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Sync($this->model->reveal())));
        $this->assertTrue($action->supports(new CreateBlik0Transaction($this->model->reveal())));
    }

    public function test_it_supports_only_payment_interface_based_models(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new CreateBlik0Transaction(new \stdClass())));
        $this->assertTrue($action->supports(new CreateBlik0Transaction($this->model->reveal())));
    }

    public function test_it_creates_blik0_transaction(): void
    {
        $createTransactionToken = $this->prophesize(TokenInterface::class);
        $createTransactionToken->getGatewayName()->willReturn('tpay');

        $this->request->getToken()->willReturn($createTransactionToken);

        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('domino@jahas.com');

        $billingAddress = $this->prophesize(AddressInterface::class);
        $billingAddress->getFullName()->willReturn('Domino Jahas');

        $order = $this->prophesize(OrderInterface::class);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getNumber()->willReturn('00000001');

        $this->model->getAmount()->willReturn(123);
        $this->model->getOrder()->willReturn($order);
        $blikCode = '777456';
        $this->model->getDetails()->willReturn([
            'blik' => $blikCode,
        ]);
        $this->model->setDetails([
            'tpay' => [
                'transaction_id' => '123awsd',
                'transaction_payment_url' => 'https://tpay.pay',
            ],
        ])->shouldBeCalled();

        $NOTIFY_URL = 'https://cw.org/notify';
        $THANK_YOU_URL = 'https://cw.org/thank-you';

        $this->tokenFactory->createToken(
            'tpay',
            $this->model,
            $NOTIFY_URL,
        )->willReturn($token = $this->prophesize(TokenInterface::class));
        $token->getTargetUrl()->willReturn($NOTIFY_URL);

        $this->router
            ->generate('sylius_shop_order_thank_you', ['_locale' => 'en_US', UrlGeneratorInterface::ABSOLUTE_URL])
            ->willReturn($THANK_YOU_URL)
        ;

        $this->router
            ->generate('commerce_weavers_tpay_payment_notification', ['_locale' => 'en_US'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($NOTIFY_URL)
        ;

        $transactionsApi = $this->prophesize(TransactionsApi::class);
        $transactionsApi->createTransaction([
            'amount' => 12.3,
            'description' => 'zamówienie #00000001',
            'payer' => [
                'email' => 'domino@jahas.com',
                'name' => 'Domino Jahas',
            ],
            'pay' => [
                'groupId' => 150,
                'blikPaymentData' => [
                    'blikToken' => $blikCode,
                ],
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => $THANK_YOU_URL,
                    'error' => $THANK_YOU_URL,
                ],
                'notification' => [
                    'url' => $NOTIFY_URL,
                ],
            ],
        ])->shouldBeCalled()->willReturn([
            'transactionId' => '123awsd',
            'transactionPaymentUrl' => 'https://tpay.pay',
        ]);

        $this->api->transactions()->willReturn($transactionsApi);
        $this->createTestSubject()->execute($this->request->reveal());
    }

    private function createTestSubject(): CreateBlik0TransactionAction
    {
        $action = new CreateBlik0TransactionAction(
            $this->router->reveal(),
            'sylius_shop_order_thank_you',
            'sylius_shop_order_thank_you',
            'commerce_weavers_tpay_payment_notification',
        );

        $action->setApi($this->api->reveal());
        $action->setGenericTokenFactory($this->tokenFactory->reveal());

        return $action;
    }
}


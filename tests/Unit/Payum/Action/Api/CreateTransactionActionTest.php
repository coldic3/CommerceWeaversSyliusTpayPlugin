<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\Payum;
use Payum\Core\Request\Sync;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Api\Transactions\TransactionsApi;

final class CreateTransactionActionTest extends TestCase
{
    use ProphecyTrait;

    private CreateTransaction|ObjectProphecy $request;

    private PaymentInterface|ObjectProphecy $model;

    private TpayApi|ObjectProphecy $api;

    private RouterInterface|ObjectProphecy $router;

    private Payum|ObjectProphecy $payum;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(CreateTransaction::class);
        $this->model = $this->prophesize(PaymentInterface::class);
        $this->api = $this->prophesize(TpayApi::class);
        $this->router = $this->prophesize(RouterInterface::class);
        $this->payum = $this->prophesize(Payum::class);

        $this->request->getModel()->willReturn($this->model->reveal());
    }

    public function it_supports_only_create_transaction_requests(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Sync($this->model->reveal())));
        $this->assertTrue($action->supports(new CreateTransaction('https://cw.org', $this->model->reveal())));
    }

    public function test_it_supports_only_payment_interface_based_models(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new CreateTransaction(new \stdClass())));
        $this->assertTrue($action->supports(new CreateTransaction($this->model->reveal())));
    }

    public function test_it_creates_transaction(): void
    {
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('maks@skalski.com');

        $billingAddress = $this->prophesize(AddressInterface::class);
        $billingAddress->getFullName()->willReturn('Maksymilian Skalski');

        $order = $this->prophesize(OrderInterface::class);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getCustomer()->willReturn($customer);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getNumber()->willReturn('00000001');

        $gatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $gatewayConfig->getGatewayName()->willReturn('tpay');

        $paymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $this->model->getMethod()->willReturn($paymentMethod);
        $this->model->getAmount()->willReturn(1234);
        $this->model->getOrder()->willReturn($order);
        $this->model->getDetails()->willReturn([]);
        $this->model->setDetails([
            'tpay' => [
                'transaction_id' => '1234abcd',
                'transaction_payment_url' => 'https://tpay.pay',
            ],
        ])->shouldBeCalled();

        $tokenFactory = $this->prophesize(GenericTokenFactory::class);
        $tokenFactory->createToken(
            'tpay',
            $this->model,
            'https://cw.org/notify',
        )->willReturn($token = $this->prophesize(TokenInterface::class));
        $token->getTargetUrl()->willReturn('https://cw.org/notify');

        $this->payum->getTokenFactory()->willReturn($tokenFactory);

        $this->router
            ->generate('sylius_shop_order_thank_you', ['_locale' => 'en_US'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://cw.org/thank-you')
        ;
        $this->router
            ->generate('commerce_weavers_tpay_payment_notification', ['_locale' => 'en_US'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://cw.org/notify')
        ;

        $transactionsApi = $this->prophesize(TransactionsApi::class);
        $transactionsApi->createTransaction([
            'amount' => 12.34,
            'description' => 'zamówienie #00000001',
            'payer' => [
                'email' => 'maks@skalski.com',
                'name' => 'Maksymilian Skalski',
            ],
            'callbacks' => [
                'payerUrls' => [
                    'success' => 'https://cw.org/thank-you',
                    'error' => 'https://cw.org/thank-you',
                ],
                'notification' => [
                    'url' => 'https://cw.org/notify'
                ],
            ],
        ])->shouldBeCalled()->willReturn([
            'transactionId' => '1234abcd',
            'transactionPaymentUrl' => 'https://tpay.pay',
        ]);

        $this->api->transactions()->willReturn($transactionsApi);

        $this->createTestSubject()->execute($this->request->reveal());
    }

    private function createTestSubject(): CreateTransactionAction
    {
        $action = new CreateTransactionAction($this->router->reveal(), $this->payum->reveal());

        $action->setApi($this->api->reveal());

        return $action;
    }
}

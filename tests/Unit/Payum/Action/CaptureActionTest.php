<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\CaptureAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Sync;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;

final class CaptureActionTest extends TestCase
{
    use ProphecyTrait;

    private GatewayInterface|ObjectProphecy $gateway;

    private Capture|ObjectProphecy $request;

    private PaymentInterface|ObjectProphecy $model;

    protected function setUp(): void
    {
        $this->gateway = $this->prophesize(GatewayInterface::class);
        $this->request = $this->prophesize(Capture::class);
        $this->model = $this->prophesize(PaymentInterface::class);

        $this->request->getModel()->willReturn($this->model->reveal());
    }

    public function it_supports_only_capture_requests(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Sync($this->model->reveal())));
        $this->assertTrue($action->supports(new Capture($this->model->reveal())));
    }

    public function test_it_supports_only_payment_interface_based_models(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Capture(new \stdClass())));
        $this->assertTrue($action->supports(new Capture($this->model->reveal())));
    }

    public function test_it_throws_http_redirect_with_using_transaction_payment_url(): void
    {
        $this->expectException(HttpRedirect::class);

        $token = $this->prophesize(TokenInterface::class);
        $token->getAfterUrl()->willReturn('http://foo.bar');

        $this->request->getToken()->willReturn($token);

        $this->gateway->execute(Argument::that(function (CreateTransaction $createTransaction): bool {
            return $createTransaction->getModel() === $this->model->reveal() && $createTransaction->getAfterUrl() === 'http://foo.bar';
        }))->shouldBeCalled();
        $this->model->getDetails()->shouldBeCalled()->willReturn([
            'tpay' => [
                'transaction_payment_url' => 'https://tpay.pay',
            ],
        ]);

        $this->createTestSubject()->execute($this->request->reveal());
    }

    private function createTestSubject(): CaptureAction
    {
        $action = new CaptureAction();

        $action->setGateway($this->gateway->reveal());

        return $action;
    }
}

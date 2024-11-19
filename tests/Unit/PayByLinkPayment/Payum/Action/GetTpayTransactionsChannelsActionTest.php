<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\PayByLinkPayment\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Action\GetTpayTransactionsChannelsAction;
use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify\NotifyData;
use CommerceWeavers\SyliusTpayPlugin\Tpay\TpayApi;
use Payum\Core\Model\PaymentInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Tpay\OpenApi\Api\Transactions\TransactionsApi;

final class GetTpayTransactionsChannelsActionTest extends TestCase
{
    use ProphecyTrait;

    private GetTpayTransactionsChannels|ObjectProphecy $request;
    private TpayApi|ObjectProphecy $api;
    private PaymentInterface|ObjectProphecy $model;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->api = $this->prophesize(TpayApi::class);
        $this->model = $this->prophesize(PaymentInterface::class);

        $this->request->getModel()->willReturn($this->model->reveal());
    }

    public function test_it_supports_only_get_bank_groups_request(): void
    {
        $action = new GetTpayTransactionsChannelsAction();

        $this->assertFalse($action->supports(new Notify(new \stdClass(), $this->createNotifyDataObject())));
        $this->assertTrue($action->supports(new GetTpayTransactionsChannels($this->model->reveal())));
    }

    public function test_it_returns_bank_groups_on_execute(): void
    {
        $transactions = $this->prophesize(TransactionsApi::class);

        $this->api->transactions()->willReturn($transactions);

        $channels = [];

        $transactions->getChannels()->willReturn($channels);
        $this->request->setResult($channels)->shouldBeCalled();

        $action = new GetTpayTransactionsChannelsAction();
        $action->setApi($this->api->reveal());
        $action->execute($this->request->reveal());
    }

    private function createNotifyDataObject(string $jws = 'jws', string $content = 'content', array $parameters = []): NotifyData
    {
        return new NotifyData($jws, $content, $parameters);
    }
}

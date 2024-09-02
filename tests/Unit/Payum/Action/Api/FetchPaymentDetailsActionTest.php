<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\FetchPaymentDetailsAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\FetchPaymentDetails;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Sync;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Tpay\OpenApi\Api\TpayApi;
use tpaySDK\Api\Transactions\TransactionsApi;

final class FetchPaymentDetailsActionTest extends TestCase
{
    use ProphecyTrait;

    private FetchPaymentDetails|ObjectProphecy $request;

    private ArrayObject|ObjectProphecy $model;

    private TpayApi|ObjectProphecy $api;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(FetchPaymentDetails::class);
        $this->model = $this->prophesize(ArrayObject::class);
        $this->api = $this->prophesize(TpayApi::class);

        $this->request->getModel()->willReturn($this->model->reveal());
    }

    public function it_supports_only_fetch_payment_details_requests(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Sync($this->model->reveal())));
        $this->assertTrue($action->supports(new FetchPaymentDetails('1234abcd', $this->model->reveal())));
    }

    public function test_it_supports_only_payment_interface_based_models(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new FetchPaymentDetails('1234abcd', new \stdClass())));
        $this->assertTrue($action->supports(new FetchPaymentDetails('1234abcd', new ArrayObject())));
    }

    public function test_it_returns_fetched_payment_details(): void
    {
        $this->request->getTransactionId()->willReturn('1234abcd');

        $transactionsApi = $this->prophesize(TransactionsApi::class);
        $transactionsApi->getTransactionById('1234abcd')->willReturn(['status' => 'correct']);

        $this->api->transactions()->willReturn($transactionsApi);

        $this->model->replace(['status' => 'correct'])->shouldBeCalled();

        $this->createTestSubject()->execute($this->request->reveal());
    }

    private function createTestSubject(): FetchPaymentDetailsAction
    {
        $action = new FetchPaymentDetailsAction();

        $action->setApi($this->api->reveal());

        return $action;
    }
}

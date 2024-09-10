<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\PayWithCardAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\PayWithCard;
use Payum\Core\Reply\HttpRedirect;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Api\Transactions\TransactionsApi;

final class PayWithCardActionTest extends TestCase
{
    use ProphecyTrait;

    private TpayApi|ObjectProphecy $api;

    protected function setUp(): void
    {
        $this->api = $this->prophesize(TpayApi::class);
    }

    public function test_it_supports_pay_with_card_request_with_a_payment_model(): void
    {
        $request = $this->prophesize(PayWithCard::class);
        $request->getModel()->willReturn($this->prophesize(PaymentInterface::class)->reveal());

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertTrue($isSupported);
    }

    public function test_it_does_not_supports_other_request(): void
    {
        $request = $this->prophesize(\stdClass::class);
        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertFalse($isSupported);
    }

    public function test_it_does_not_supports_pay_with_card_request_with_non_payment_model(): void
    {
        $request = $this->prophesize(PayWithCard::class);
        $request->getModel()->willReturn($this->prophesize(\stdClass::class)->reveal());

        $isSupported = $this->createTestSubject()->supports($request->reveal());

        $this->assertFalse($isSupported);
    }


    public function test_it_executes_pay_with_card_request_successfully(): void
    {
        $request = $this->prophesize(PayWithCard::class);
        $paymentModel = $this->prophesize(PaymentInterface::class);
        $details = [
            'tpay' => [
                'card' => 'test-card',
                'transaction_id' => 12345,
            ],
        ];

        $response = [
            'transactionPaymentUrl' => 'http://example.com',
            'status' => 'completed',
        ];

        $request->getModel()->willReturn($paymentModel->reveal());
        $paymentModel->getDetails()->willReturn($details);

        $transactions = $this->prophesize(TransactionsApi::class);
        $transactions->createPaymentByTransactionId([
            'groupId' => 103,
            'cardPaymentData' => [
                'card' => $details['tpay']['card'],
            ],
        ], $details['tpay']['transaction_id'])->willReturn($response);

        $this->api->transactions()->willReturn($transactions);

        $paymentModel->setDetails([
            'tpay' => [
                'transaction_id' => 12345,
                'transaction_payment_url' => 'http://example.com',
            ],
        ])->shouldBeCalled();

        $subject = $this->createTestSubject();

        $subject->execute($request->reveal());
    }

    public function test_it_throws_http_redirect_for_pending_status(): void
    {
        $this->expectException(HttpRedirect::class);

        $request = $this->prophesize(PayWithCard::class);
        $paymentModel = $this->prophesize(PaymentInterface::class);
        $details = [
            'tpay' => [
                'card' => 'test-card',
                'transaction_id' => 12345,
            ],
        ];

        $response = [
            'transactionPaymentUrl' => 'http://example.com',
            'status' => 'pending',
        ];

        $request->getModel()->willReturn($paymentModel->reveal());
        $paymentModel->getDetails()->willReturn($details);

        $transactions = $this->prophesize(TransactionsApi::class);
        $transactions->createPaymentByTransactionId([
            'groupId' => 103,
            'cardPaymentData' => [
                'card' => $details['tpay']['card'],
            ],
        ], $details['tpay']['transaction_id'])->willReturn($response);

        $this->api->transactions()->willReturn($transactions);

        $paymentModel->setDetails([
            'tpay' => [
                'transaction_id' => 12345,
                'transaction_payment_url' => 'http://example.com',
            ],
        ])->shouldBeCalled();

        $subject = $this->createTestSubject();

        $subject->execute($request->reveal());
    }

    private function createTestSubject(): PayWithCardAction
    {
        $action = new PayWithCardAction();

        $action->setApi($this->api->reveal());

        return $action;
    }
}

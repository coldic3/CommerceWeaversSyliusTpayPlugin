<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\SaveCreditCardAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\SaveCreditCard;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\BasicPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifierInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\TpayApi;
use Payum\Core\Request\Sync;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Tests\CommerceWeavers\SyliusTpayPlugin\Helper\PaymentDetailsHelperTrait;

final class SaveCreditCardActionTest extends TestCase
{
    use ProphecyTrait;

    use PaymentDetailsHelperTrait;

    private SaveCreditCard $request;

    private PaymentInterface|ObjectProphecy $model;

    private CustomerInterface|ObjectProphecy $customer;

    private TpayApi|ObjectProphecy $api;

    private BasicPaymentFactoryInterface|ObjectProphecy $factory;

    private ChecksumVerifierInterface|ObjectProphecy $repository;

    protected function setUp(): void
    {
        $this->model = $this->prophesize(PaymentInterface::class);
        $this->api = $this->prophesize(TpayApi::class);
        $this->factory = $this->prophesize(FactoryInterface::class);
        $this->repository = $this->prophesize(RepositoryInterface::class);

        $order = $this->prophesize(OrderInterface::class);
        $order->getLocaleCode()->willReturn('en_US');

        $this->customer = $this->prophesize(CustomerInterface::class);
        $order->getCustomer()->willReturn($this->customer->reveal());

        $this->model = $this->prophesize(PaymentInterface::class);
        $this->model->getOrder()->willReturn($order->reveal());
        $this->model->getDetails()->willReturn([]);

        $token = $this->prophesize(TokenInterface::class);
        $token->getGatewayName()->willReturn('tpay');

        $this->request = new SaveCreditCard($token->reveal(), 'card_token', 'card_brand', 'card_tail', '1128');
        $this->request->setModel($this->model->reveal());
    }

    public function test_it_supports_only_save_credit_card_requests(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new Sync($this->model->reveal())));
        $this->assertTrue($action->supports($this->request));
    }

    public function test_it_supports_only_payment_interface_based_models(): void
    {
        $action = $this->createTestSubject();

        $this->assertFalse($action->supports(new SaveCreditCard(new \stdClass(), 'card_token', 'card_brand', 'card_tail', 'token_expiry_date')));
        $this->assertTrue($action->supports($this->request));
    }

    public function test_it_saves_returned_credit_card(): void
    {
        $creditCard = $this->prophesize(CreditCardInterface::class);
        $this->factory->createNew()->willReturn($creditCard->reveal());

        $this->model->setDetails($this->getExpectedDetails())->shouldBeCalled();

        $creditCard->setTail('card_tail')->shouldBeCalled();
        $creditCard->setBrand('card_brand')->shouldBeCalled();
        $creditCard->setToken('card_token')->shouldBeCalled();
        $creditCard->setExpirationDate(new \DateTimeImmutable('01-11-2028'))->shouldBeCalled();
        $creditCard->setCustomer($this->customer)->shouldBeCalled();

        $this->repository->add($creditCard->reveal())->shouldBeCalled();

        $this->createTestSubject()->execute($this->request);
    }

    private function createTestSubject(): SaveCreditCardAction
    {
        $action = new SaveCreditCardAction(
            $this->factory->reveal(),
            $this->repository->reveal(),
        );

        $action->setApi($this->api->reveal());

        return $action;
    }
}

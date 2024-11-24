<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\PayByLinkPayment\ContextProvider;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\ContextProvider\BankListContextProvider;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

class BankListContextProviderTest extends TestCase
{
    use ProphecyTrait;

    private ValidTpayChannelListProviderInterface|ObjectProphecy $validTpayChannelListProvider;
    protected function setUp(): void
    {
        $this->validTpayChannelListProvider = $this->prophesize(ValidTpayChannelListProviderInterface::class);
    }

    public function test_it_does_not_support_some_template_event_and_pay_by_link_template_name(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'sylius.shop.checkout.some_template', null, null, null, null);

        $supports = $this->createTestObject()->supports($templateBlock);

        $this->assertFalse($supports);
    }

    public function test_it_supports_complete_summary_template_event_and_pay_by_link_template_name(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'sylius.shop.checkout.complete.summary', null, null, null, null);

        $supports = $this->createTestObject()->supports($templateBlock);

        $this->assertTrue($supports);
    }

    public function test_it_supports_select_payment_choice_item_form_template_event_and_pay_by_link_template_name(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'cw.tpay.shop.select_payment.choice_item_form', null, null, null, null);

        $supports = $this->createTestObject()->supports($templateBlock);

        $this->assertTrue($supports);
    }

    public function test_it_provides_banks_for_context_if_there_is_no_order_in_template_context(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'cw.tpay.shop.select_payment.choice_item_form', null, null, null, null);

        $this->validTpayChannelListProvider->provide()->shouldBeCalled();
        $this->validTpayChannelListProvider->provide()->willReturn(['1' => 'some bank']);

        $context = $this->createTestObject()->provide(
            ['i_am_not_an_order' => 'some_context'],
            $templateBlock
        );

        $this->assertArrayHasKey('banks', $context);
        $this->assertSame(['1' => 'some bank'], $context['banks']);
    }

    public function test_it_provides_nothing_new_for_context_if_order_has_no_payment(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'cw.tpay.shop.select_payment.choice_item_form', null, null, null, null);
        $order = $this->prophesize(OrderInterface::class);
        $order->getLastPayment()->willReturn(null);

        $this->validTpayChannelListProvider->provide()->shouldNotBeCalled();
        $this->validTpayChannelListProvider->provide()->willReturn(['1' => 'some bank']);

        $context = $this->createTestObject()->provide(
            ['order' => $order->reveal()],
            $templateBlock
        );

        $this->assertArrayNotHasKey('banks', $context);
    }

    public function test_it_provides_bank_list_as_context_if_order_has_payment(): void
    {
        $templateBlock = new TemplateBlock('pay_by_link', 'cw.tpay.shop.select_payment.choice_item_form', null, null, null, null);
        $order = $this->prophesize(OrderInterface::class);
        $payment = $this->prophesize(PaymentInterface::class);
        $order->getLastPayment()->willReturn($payment);

        $this->validTpayChannelListProvider->provide()->shouldBeCalled();
        $this->validTpayChannelListProvider->provide()->willReturn(['1' => 'some bank']);

        $context = $this->createTestObject()->provide(
            ['order' => $order->reveal()],
            $templateBlock
        );

        $this->assertArrayHasKey('banks', $context);
        $this->assertSame(['1' => 'some bank'], $context['banks']);
    }

    private function createTestObject(): BankListContextProvider
    {
        return new BankListContextProvider($this->validTpayChannelListProvider->reveal());
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Form\EventListener;

use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\RemoveUnnecessaryPaymentDetailsFieldsListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

final class RemoveUnnecessaryPaymentDetailsFieldsListenerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_removes_card_field_once_card_data_is_not_set(): void
    {
        $form = $this->prophesize(FormInterface::class);
        $form->remove('card')->shouldBeCalled()->willReturn($form);
        $form->remove('blik_token')->shouldNotBeCalled();

        $event = new FormEvent($form->reveal(), ['blik_token' => '123456']);

        $this->createTestSubject()->__invoke($event);
    }

    public function test_it_removes_blik_token_field_once_blik_token_is_not_set(): void
    {
        $form = $this->prophesize(FormInterface::class);
        $form->remove('card')->shouldNotBeCalled();
        $form->remove('blik_token')->shouldBeCalled()->willReturn($form);

        $event = new FormEvent($form->reveal(), ['card' => 'h45h']);

        $this->createTestSubject()->__invoke($event);
    }

    public function test_it_removes_both_card_and_blik_token_if_none_of_them_is_passed(): void
    {
        $form = $this->prophesize(FormInterface::class);
        $form->remove('card')->shouldBeCalled()->willReturn($form);
        $form->remove('blik_token')->shouldBeCalled()->willReturn($form);

        $event = new FormEvent($form->reveal(), []);

        $this->createTestSubject()->__invoke($event);
    }

    public function createTestSubject(): object
    {
        return new RemoveUnnecessaryPaymentDetailsFieldsListener();
    }
}

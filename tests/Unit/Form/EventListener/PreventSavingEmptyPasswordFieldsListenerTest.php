<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Form\EventListener;

use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\PreventSavingEmptyPasswordFieldsListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;

final class PreventSavingEmptyPasswordFieldsListenerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_marks_client_secret_as_not_mapped_when_client_secret_is_not_set(): void
    {
        $data = ['notification_security_code' => 'abcd'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldBeCalled()->willReturn($form);
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }

    public function test_it_marks_client_secret_as_not_mapped_when_client_secret_is_an_empty_string(): void
    {
        $data = ['client_secret' => '', 'notification_security_code' => 'abcd'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldBeCalled()->willReturn($form);
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }

    public function test_it_does_nothing_when_client_secret_is_set(): void
    {

        $data = ['client_secret' => 'some_secret', 'notification_security_code' => 'abcd'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldNotBeCalled();
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldNotBeCalled();

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }

    public function test_it_marks_notification_security_code_as_not_mapped_when_notification_security_code_is_not_set(): void
    {
        $data = ['client_secret' => 'abcd'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('notification_security_code')->shouldBeCalled()->willReturn($form);
        $form->add('notification_security_code', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.notification_security_code',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }

    public function test_it_marks_notification_security_code_as_not_mapped_when_notification_security_code_is_an_empty_string(): void
    {
        $data = ['client_secret' => 'abcd', 'notification_security_code' => ''];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('notification_security_code')->shouldBeCalled()->willReturn($form);
        $form->add('notification_security_code', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.notification_security_code',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }

    public function test_it_does_nothing_when_notification_security_code_is_set(): void
    {
        $data = ['client_secret' => 'abcd', 'notification_security_code' => 'some_secret'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('notification_security_code')->shouldNotBeCalled();
        $form->add('notification_security_code', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.notification_security_code',
            'mapped' => false,
        ])->shouldNotBeCalled();

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyPasswordFieldsListener())($event);
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Form\EventListener;

use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\PreventSavingEmptyClientSecretListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;

final class PreventSavingEmptyClientSecretListenerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_marks_client_secret_as_not_mapped_when_client_secret_is_not_set(): void
    {
        $data = [];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldBeCalled()->willReturn($form);
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyClientSecretListener())($event);
    }

    public function test_it_marks_client_secret_as_not_mapped_when_client_secret_is_an_empty_string(): void
    {
        $data = ['client_secret' => ''];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldBeCalled()->willReturn($form);
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldBeCalled()->willReturn($form);

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyClientSecretListener())($event);
    }

    public function test_it_does_nothing_when_client_secret_is_set(): void
    {

        $data = ['client_secret' => 'some_secret'];
        $form = $this->prophesize(FormInterface::class);

        $form->remove('client_secret')->shouldNotBeCalled();
        $form->add('client_secret', PasswordType::class, [
            'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
            'mapped' => false,
        ])->shouldNotBeCalled();

        $event = new PreSubmitEvent($form->reveal(), $data);

        (new PreventSavingEmptyClientSecretListener())($event);
    }
}

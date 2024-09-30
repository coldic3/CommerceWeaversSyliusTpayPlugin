<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\EventListener;

use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class PreventSavingEmptyPasswordFieldsListener
{
    public function __invoke(PreSubmitEvent $preSubmitEvent): void
    {
        /** @var array<string, mixed> $data */
        $data = $preSubmitEvent->getData();
        $form = $preSubmitEvent->getForm();

        if (!isset($data['client_secret']) || '' === $data['client_secret']) {
            $form->remove('client_secret');
            $form->add(
                'client_secret',
                PasswordType::class,
                [
                    'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.client_secret',
                    'mapped' => false,
                ],
            );
        }

        if (!isset($data['notification_security_code']) || '' === $data['notification_security_code']) {
            $form->remove('notification_security_code');
            $form->add(
                'notification_security_code',
                PasswordType::class,
                [
                    'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.notification_security_code',
                    'mapped' => false,
                ],
            );
        }
    }
}

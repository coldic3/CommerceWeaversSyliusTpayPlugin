<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\EventListener;

use Symfony\Component\Form\FormEvent;

final class RemoveUnnecessaryPaymentDetailsFieldsListener
{
    public function __invoke(FormEvent $event): void
    {
        /** @var array{card?: string, blik_token?: string} $data */
        $data = $event->getData() ?? [];
        $form = $event->getForm();

        if (!isset($data['card'])) {
            $form->remove('card');
        }

        if (!isset($data['blik_token'])) {
            $form->remove('blik_token');
        }
    }
}
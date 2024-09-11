<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TpayPaymentDetailsType extends AbstractType
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'card',
                TpayCardType::class,
                [
                    'property_path' => '[card]',
                ],
            )
            ->add(
                'blik_token',
                TextType::class,
                [
                    'property_path' => '[blik_token]',
                    'label' => 'commerce_weavers_sylius_tpay.shop.order_summary.blik.token',
                ],
            )
        ;

        /** TODO: Extract into a separate class */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            /** @var array{card?: string, blik_token?: string} $data */
            $data = $event->getData() ?? [];
            $form = $event->getForm();

            if (!isset($data['card'])) {
                $form->remove('card');
            }

            if (!isset($data['blik_token'])) {
                $form->remove('blik_token');
            }
        });
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\Extension;

use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayPaymentDetailsType;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\PaymentDetailsType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CompleteTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'tpay',
                TpayPaymentDetailsType::class,
                [
                    'property_path' => 'last_payment.details[tpay]',
                ],
            )
        ;
        $builder->add('others', PaymentDetailsType::class, [
            'label' => 'commerce_weavers_sylius_tpay.payment.blik.token',
//          TODO some validation that works becuase this kind does not
//            'constraints' => [
//                new Length(['value' => 6, 'min' => 6, 'max' => 6, 'groups' => ['sylius']]),
//            ],
            'property_path' => 'last_new_payment.details[tpay]',
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CompleteType::class];
    }
}

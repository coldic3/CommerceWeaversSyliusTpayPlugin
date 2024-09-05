<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class CompleteTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('notes', TextareaType::class, [
            'label' => 'sylius.form.notes',
            'required' => false,
        ]);

        $builder->add('others', PaymentDetailsType::class, [
            'label' => 'commerce_weavers_sylius_tpay.payment.blik.token',
            // TODO missing validation
            'property_path' => 'payments[0].details', // TODO looks awfull and what about other payments?
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CompleteType::class];
    }
}

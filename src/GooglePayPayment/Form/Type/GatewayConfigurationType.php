<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Form\Type;

use CommerceWeavers\SyliusTpayPlugin\Form\Type\AbstractTpayGatewayConfigurationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class GatewayConfigurationType extends AbstractTpayGatewayConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'google_merchant_id',
                TextType::class,
                [
                    'empty_data' => '',
                    'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.google_merchant_id',
                    'required' => true,
                    'priority' => -250,
                    'constraints' => [
                        new NotBlank(allowNull: false, groups: ['sylius']),
                    ],
                    'validation_groups' => ['sylius'],
                ],
            )
        ;
    }
}

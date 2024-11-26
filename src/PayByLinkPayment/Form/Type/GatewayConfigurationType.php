<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Form\Type;

use CommerceWeavers\SyliusTpayPlugin\Form\Type\AbstractTpayGatewayConfigurationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class GatewayConfigurationType extends AbstractTpayGatewayConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'tpay_channel_id',
                TextType::class,
                [
                    'empty_data' => '',
                    'label' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.tpay_channel_id',
                    'help' => 'commerce_weavers_sylius_tpay.admin.gateway_configuration.tpay_channel_id_help',
                    'required' => false,
                ],
            )
        ;
    }
}

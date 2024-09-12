<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Form\DataTransformer\CardTypeDataTransformer;
use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\PreventSavingEmptyClientSecretListener;
use CommerceWeavers\SyliusTpayPlugin\Form\Extension\CompleteTypeExtension;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayCardType;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayGatewayConfigurationType;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayPaymentDetailsType;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.form.data_transformer.card_type', CardTypeDataTransformer::class);

    $services->set(CompleteTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(TpayGatewayConfigurationType::class)
        ->args([
            service(PreventSavingEmptyClientSecretListener::class),
        ])
        ->tag(
            'sylius.gateway_configuration_type',
            ['label' => 'commerce_weavers_sylius_tpay.admin.name', 'type' => TpayGatewayFactory::NAME],
        )
        ->tag('form.type')
    ;

    $services->set(TpayCardType::class)
        ->args([
            service('commerce_weavers_tpay.form.data_transformer.card_type'),
        ])
        ->tag('form.type')
    ;

    $services->set(TpayPaymentDetailsType::class)->tag('form.type');

    $services->set(PreventSavingEmptyClientSecretListener::class);
};

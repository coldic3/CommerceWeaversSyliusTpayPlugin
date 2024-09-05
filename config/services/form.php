<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\PreventSavingEmptyClientSecretListener;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\CompleteTypeExtension;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayGatewayConfigurationType;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

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

    $services->set(PreventSavingEmptyClientSecretListener::class);

    $services->set(CompleteTypeExtension::class)->tag('form.type_extension');
};

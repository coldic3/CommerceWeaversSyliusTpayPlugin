<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator\CallbackUrlGenerator;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Routing\Generator\CallbackUrlGeneratorInterface;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.tpay.routing.generator.callback_url', CallbackUrlGenerator::class)
        ->args([
            service('router'),
            param('commerce_weavers_sylius_tpay.payum.create_transaction.success_route'),
            param('commerce_weavers_sylius_tpay.payum.create_transaction.failure_route'),
        ])
        ->alias(CallbackUrlGeneratorInterface::class, 'commerce_weavers_sylius_tpay.tpay.routing.generator.callback_url')
    ;
};

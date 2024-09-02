<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(TpayGatewayFactory::class, GatewayFactoryBuilder::class)
        ->args([
            TpayGatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => TpayGatewayFactory::NAME])
    ;
};

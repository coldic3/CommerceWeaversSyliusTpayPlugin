<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Payum\Factory\GatewayFactory;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.google_pay_payment.payum.factory.gateway', GatewayFactoryBuilder::class)
        ->args([
            GatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => GatewayFactory::NAME])
    ;
};

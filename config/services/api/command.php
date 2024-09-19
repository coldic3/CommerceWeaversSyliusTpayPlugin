<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByBlikHandler;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayHandler;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.api.command.pay_handler', PayHandler::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler')
    ;

    $services->set('commerce_weavers_tpay.api.command.pay_by_blik_handler', PayByBlikHandler::class)
        ->args([
            service('sylius.repository.payment'),
            service('payum'),
            service('commerce_weavers.tpay.payum.factory.create_transaction'),
        ])
        ->tag('messenger.message_handler')
    ;
};

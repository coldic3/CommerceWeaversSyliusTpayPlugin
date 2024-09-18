<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayHandler;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.api.command.pay_handler', PayHandler::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.manager.payment'),
            service('payum'),
        ])
        ->tag('messenger.message_handler')
    ;
};

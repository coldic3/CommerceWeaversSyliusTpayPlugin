<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcher;
use CommerceWeavers\SyliusTpayPlugin\Refunding\Dispatcher\RefundDispatcherInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers.tpay.refunding.dispatcher.refund', RefundDispatcher::class)
        ->public()
        ->args([
            service('payum'),
        ])
        ->alias(RefundDispatcherInterface::class, 'commerce_weavers.tpay.refunding.dispatcher.refund');
    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\PaymentNotificationAction;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PaymentNotificationAction::class)
        ->args([
            service('payum'),
            service('commerce_weavers.tpay.payum.factory.notify'),
        ])
        ->tag('controller.service_arguments');
    ;
};

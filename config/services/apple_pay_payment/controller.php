<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Controller\InitApplePayPaymentAction;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(InitApplePayPaymentAction::class)
        ->args([
            service('payum'),
            service('sylius.context.cart.composite'),
        ])
        ->tag('controller.service_arguments')
    ;
};

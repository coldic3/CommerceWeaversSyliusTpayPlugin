<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $container->import('services/**/*.php');

    $parameters = $container->parameters();
    $parameters
        ->set('env(TPAY_CLIENT_ID)', '')
        ->set('env(TPAY_CLIENT_SECRET)', '')
        ->set('commerce_weavers_tpay.payum.create_transaction.success_route', 'sylius_shop_order_thank_you')
        ->set('commerce_weavers_tpay.payum.create_transaction.error_route', 'sylius_shop_order_thank_you')
        ->set('commerce_weavers_tpay.payum.create_transaction.notify_route', 'commerce_weavers_tpay_payment_notification')
    ;
};

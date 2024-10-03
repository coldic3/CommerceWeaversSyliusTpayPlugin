<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    $container->import('config/**/*.php');

    $parameters = $container->parameters();

    $parameters->set('commerce_weavers_tpay.certificate.cache_ttl_in_seconds', 300);
    $parameters->set('commerce_weavers_tpay.waiting_for_payment.refresh_interval', 5);
};

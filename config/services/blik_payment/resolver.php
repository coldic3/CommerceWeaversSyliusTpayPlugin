<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Resolver\BlikAliasResolver;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Resolver\BlikAliasResolverInterface;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.blik_payment.resolver.blik_alias', BlikAliasResolver::class)
        ->args([
            service('commerce_weavers_sylius_tpay.repository.blik_alias'),
            service('commerce_weavers_sylius_tpay.factory.blik_alias'),
            service('sylius.context.channel'),
        ])
        ->alias(BlikAliasResolverInterface::class, 'commerce_weavers_sylius_tpay.blik_payment.resolver.blik_alias')
    ;
};

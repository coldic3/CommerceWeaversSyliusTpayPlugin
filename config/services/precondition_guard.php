<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\ActiveBlikAliasPreconditionGuard;
use CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\ActiveBlikAliasPreconditionGuardInterface;
use Psr\Clock\ClockInterface;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.precondition_guard.active_blik_alias', ActiveBlikAliasPreconditionGuard::class)
        ->args([
            service(ClockInterface::class),
        ])
        ->alias(ActiveBlikAliasPreconditionGuardInterface::class, 'commerce_weavers_sylius_tpay.precondition_guard.active_blik_alias')
    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint\BlikTokenRequiredValidator;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.api.validator.constraint.blik_token_required_validator', BlikTokenRequiredValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('payum.dynamic_gateways.cypher'),
        ])
        ->tag('validator.constraint_validator')
    ;
};

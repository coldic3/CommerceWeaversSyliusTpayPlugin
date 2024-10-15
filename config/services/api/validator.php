<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint\NotBlankIfGatewayConfigTypeEqualsValidator;
use CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint\TpayChannelIdEligibilityValidator;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.api.validator.constraint.not_blank_if_payment_method_type_equals', NotBlankIfGatewayConfigTypeEqualsValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('payum.dynamic_gateways.cypher'),
        ])
        ->tag('validator.constraint_validator')
    ;

    $services->set('commerce_weavers_sylius_tpay.api.validator.constraint.tpay_channel_id_eligibility_validator', TpayChannelIdEligibilityValidator::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.resolver.cached_tpay_transaction_channel_resolver'),
        ])
        ->tag('validator.constraint_validator')
    ;
};

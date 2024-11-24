<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Validator\Constraint\ValidTpayChannelValidator;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.pay_by_link_payment.validator.constraint.valid_tpay_channel', ValidTpayChannelValidator::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.provider.validated_tpay_api_bank_list'),
        ])
        ->tag('validator.constraint_validator')
    ;
};

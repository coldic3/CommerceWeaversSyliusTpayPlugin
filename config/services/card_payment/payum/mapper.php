<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Mapper\PayWithCardActionPayloadMapper;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->public()
    ;

    $services->set('commerce_weavers_sylius_tpay.card_payment.payum.mapper.pay_with_card_action', PayWithCardActionPayloadMapper::class)
        ->args([
            service('commerce_weavers_sylius_tpay.repository.credit_card'),
        ])
    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\VisaMobilePayment\Payum\Action\CreateVisaMobileTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\VisaMobilePayment\Payum\Factory\GatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CreateVisaMobileTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_visa_mobile_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_visa_mobile.create_visa_mobile_transaction'])
    ;
};

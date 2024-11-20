<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Payum\Action\CreateBlikLevelZeroTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Payum\Factory\GatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set('commerce_weavers_sylius_tpay.blik_payment.payum.action.create_blik_level_zero_transaction', CreateBlikLevelZeroTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_blik_level_zero_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
            service('commerce_weavers_sylius_tpay.repository.blik_alias'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_blik.create_blik_level_zero_transaction'])
    ;
};

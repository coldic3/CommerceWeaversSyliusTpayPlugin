<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\RedirectPayment\Payum\Action\CreateRedirectBasedTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\RedirectPayment\Payum\Factory\GatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set('commerce_weavers_sylius_tpay.redirect_payment.payum.action.create_redirect_based_transaction', CreateRedirectBasedTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_redirect_based_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_redirect.create_redirect_based_transaction'])
    ;
};

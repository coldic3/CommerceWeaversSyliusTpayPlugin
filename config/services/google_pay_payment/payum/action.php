<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Payum\Action\CreateGooglePayTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Payum\Factory\GatewayFactory as GooglePayGatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CreateGooglePayTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_google_pay_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => GooglePayGatewayFactory::NAME, 'alias' => 'cw.tpay_google_pay.create_google_pay_transaction'])
    ;
};

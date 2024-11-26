<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Action\CreateApplePayTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Action\InitializeApplePayPaymentAction;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\GatewayFactory as ApplePayGatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CreateApplePayTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_apple_pay_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => ApplePayGatewayFactory::NAME, 'alias' => 'cw.tpay_apple_pay.create_apple_pay_transaction'])
    ;

    $services->set(InitializeApplePayPaymentAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_initialize_apple_pay_payment_payload'),
        ])
        ->tag('payum.action', ['factory' => ApplePayGatewayFactory::NAME, 'alias' => 'cw.tpay_apple_pay.initialize_apple_pay_payment'])
    ;
};

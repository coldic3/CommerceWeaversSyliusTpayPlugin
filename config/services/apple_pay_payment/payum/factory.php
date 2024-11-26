<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\GatewayFactory;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactory;
use CommerceWeavers\SyliusTpayPlugin\ApplePayPayment\Payum\Factory\InitializeApplePayPaymentFactoryInterface;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.apple_pay_payment.payum.factory.gateway', GatewayFactoryBuilder::class)
        ->args([
            GatewayFactory::class,
        ])
        ->tag('payum.gateway_factory_builder', ['factory' => GatewayFactory::NAME])
    ;

    $services->set('commerce_weavers_sylius_tpay.payum.factory.initialize_apple_pay_payment', InitializeApplePayPaymentFactory::class)
        ->alias(InitializeApplePayPaymentFactoryInterface::class, 'commerce_weavers_sylius_tpay.payum.factory.initialize_apple_pay_payment')
    ;
};

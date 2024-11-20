<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Tpay\Factory\CreateCardPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Tpay\Factory\CreateCardPaymentPayloadFactoryInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.card_payment.tpay.factory.create_card_payment_payload', CreateCardPaymentPayloadFactory::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_redirect_based_payment_payload'),
        ])
        ->alias(CreateCardPaymentPayloadFactoryInterface::class, 'commerce_weavers_sylius_tpay.card_payment.tpay.factory.create_card_payment_payload')
    ;
};

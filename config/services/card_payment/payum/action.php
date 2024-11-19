<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Action\CreateCardTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Action\PayWithCardAction;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Action\SaveCreditCardAction;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Factory\GatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CreateCardTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.card_payment.tpay.factory.create_card_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_card.create_card_transaction'])
    ;

    $services->set(PayWithCardAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.card_payment.payum.mapper.pay_with_card_action'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_card.pay_with_card'])
    ;

    $services->set(SaveCreditCardAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.factory.credit_card'),
            service('commerce_weavers_sylius_tpay.repository.credit_card'),
        ])
        ->tag('payum.action', ['factory' => GatewayFactory::NAME, 'alias' => 'cw.tpay_card.save_credit_card'])
    ;
};

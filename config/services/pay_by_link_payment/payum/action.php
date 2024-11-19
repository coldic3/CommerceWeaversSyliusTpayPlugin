<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Action\CreatePayByLinkTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Action\GetTpayTransactionsChannelsAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->public()
    ;

    $services->set(CreatePayByLinkTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_pay_by_link_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay_pbl.create_pay_by_link_transaction'])
    ;

    $services->set(GetTpayTransactionsChannelsAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay_pbl.get_transactions_channels'])
    ;
};

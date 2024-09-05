<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateBlik0TransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\FetchPaymentDetailsAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\CaptureAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\GetStatusAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->public()
    ;

    $services->set(CaptureAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.capture'])
    ;

    $services->set(CreateTransactionAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_transaction'])
    ;

    $services->set(CreateBlik0TransactionAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_blik0_transaction'])
    ;

    $services->set(FetchPaymentDetailsAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.fetch_payment_details'])
    ;

    $services->set(GetStatusAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.get_status'])
    ;
};

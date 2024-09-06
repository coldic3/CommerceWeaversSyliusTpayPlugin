<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateBlik0TransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\NotifyAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\CaptureAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\GetStatusAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return function(ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->public()
    ;

    $services->set(CaptureAction::class)
        ->args([
            service('commerce_weavers.tpay.payum.factory.create_transaction'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.capture'])
    ;

    $services->set(CreateTransactionAction::class)
        ->args([
            service('router'),
            param('commerce_weavers_tpay.payum.create_transaction.success_route'),
            param('commerce_weavers_tpay.payum.create_transaction.error_route'),
            param('commerce_weavers_tpay.payum.create_transaction.notify_route'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_transaction'])
    ;

    $services->set(NotifyAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.notify'])
    ;

    $services->set(GetStatusAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.get_status'])
    ;

    $services->set(CreateBlik0TransactionAction::class)
        ->args([
            service('router'),
            param('commerce_weavers_tpay.payum.create_transaction.success_route'),
            param('commerce_weavers_tpay.payum.create_transaction.error_route'),
            param('commerce_weavers_tpay.payum.create_transaction.notify_route'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_blik0_transaction'])
    ;
};

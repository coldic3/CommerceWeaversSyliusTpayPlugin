<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateApplePayTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateBlikLevelZeroTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateCardTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateGooglePayTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreatePayByLinkTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateRedirectBasedTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\CreateVisaMobileTransactionAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\GetTpayTransactionsChannelsAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\InitializeApplePayPaymentAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\NotifyAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\PayWithCardAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\SaveCreditCardAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\CaptureAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\GetStatusAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\RefundAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\ResolveNextRouteAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;
use CommerceWeavers\SyliusTpayPlugin\Payum\Mapper\PayWithCardActionPayloadMapper;
use CommerceWeavers\SyliusTpayPlugin\Payum\Mapper\PayWithCardActionPayloadMapperInterface;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->public()
    ;

    $services->set('commerce_weavers_sylius_tpay.payum.mapper.pay_with_card_action', PayWithCardActionPayloadMapper::class)
        ->args([
            service('commerce_weavers_sylius_tpay.repository.credit_card'),
        ])
    ;
};

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
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\PartialRefundAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\RefundAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\ResolveNextRouteAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->public()
    ;

    $services->set(CaptureAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.payum.factory.create_transaction'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.capture'])
    ;

    $services->set(CreateCardTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_card_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_card_transaction'])
    ;

    $services->set(CreateApplePayTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_apple_pay_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_apple_pay_transaction'])
    ;

    $services->set(CreateGooglePayTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_google_pay_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_google_pay_transaction'])
    ;

    $services->set(CreateBlikLevelZeroTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_blik_level_zero_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
            service('commerce_weavers_sylius_tpay.repository.blik_alias'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_blik_level_zero_transaction'])
    ;

    $services->set(CreateRedirectBasedTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_redirect_based_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_redirect_based_transaction'])
    ;

    $services->set(CreateVisaMobileTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_visa_mobile_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_visa_mobile_transaction'])
    ;

    $services->set(NotifyAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.security.notification.factory.basic_payment'),
            service('commerce_weavers_sylius_tpay.tpay.security.notification.verifier.checksum'),
            service('commerce_weavers_sylius_tpay.tpay.security.notification.verifier.signature'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.notify'])
    ;

    $services->set(SaveCreditCardAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.factory.credit_card'),
            service('commerce_weavers_sylius_tpay.repository.credit_card'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.save_credit_card'])
    ;

    $services->set(PayWithCardAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.payum.mapper.pay_with_card_action'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.pay_with_card'])
    ;

    $services->set(GetStatusAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.get_status'])
    ;

    $services->set(PartialRefundAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.partial_refund'])
    ;

    $services->set(RefundAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.refund'])
    ;

    $services->set(GetTpayTransactionsChannelsAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.get_transactions_channels'])
    ;

    $services->set(InitializeApplePayPaymentAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_initialize_apple_pay_payment_payload'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.initialize_apple_pay_payment'])
    ;

    $services->set(CreatePayByLinkTransactionAction::class)
        ->args([
            service('commerce_weavers_sylius_tpay.tpay.factory.create_pay_by_link_payment_payload'),
            service('commerce_weavers_sylius_tpay.payum.factory.token.notify'),
        ])
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.create_pay_by_link_transaction'])
    ;

    $services->set(ResolveNextRouteAction::class)
        ->tag('payum.action', ['factory' => TpayGatewayFactory::NAME, 'alias' => 'cw.tpay.resolve_next_route'])
    ;
};

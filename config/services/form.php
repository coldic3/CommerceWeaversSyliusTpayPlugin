<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\AddSavedCreditCardsListener;
use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\DecryptGatewayConfigListener;
use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\EncryptGatewayConfigListener;
use CommerceWeavers\SyliusTpayPlugin\Form\EventListener\RemoveUnnecessaryPaymentDetailsFieldsListener;
use CommerceWeavers\SyliusTpayPlugin\Form\Extension\CompleteTypeExtension;
use CommerceWeavers\SyliusTpayPlugin\Form\Extension\PaymentTypeExtension;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\AbstractTpayGatewayConfigurationType;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayGatewayConfigurationType;
use CommerceWeavers\SyliusTpayPlugin\Form\Type\TpayPaymentDetailsType;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\TpayGatewayFactory;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    /** new */

    $services->set('commerce_weavers_sylius_tpay.form.type.abstract_tpay_gateway_configuration', AbstractTpayGatewayConfigurationType::class)
        ->abstract()
        ->args([
            service('commerce_weavers_sylius_tpay.form.event_listener.decrypt_gateway_config'),
            service('commerce_weavers_sylius_tpay.form.event_listener.encrypt_gateway_config'),
        ])
    ;

    /** end new */

    $services->set(CompleteTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(PaymentTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(TpayGatewayConfigurationType::class)
        ->args([
            service('commerce_weavers_sylius_tpay.form.event_listener.decrypt_gateway_config'),
            service('commerce_weavers_sylius_tpay.form.event_listener.encrypt_gateway_config'),
        ])
        ->tag(
            'sylius.gateway_configuration_type',
            ['label' => 'commerce_weavers_sylius_tpay.admin.name', 'type' => TpayGatewayFactory::NAME],
        )
        ->tag('form.type')
    ;


    $services->set(TpayPaymentDetailsType::class)
        ->args([
            service('commerce_weavers_sylius_tpay.form.event_listener.remove_unnecessary_payment_details_fields'),
            service('security.token_storage'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('commerce_weavers_sylius_tpay.form.event_listener.decrypt_gateway_config', DecryptGatewayConfigListener::class)
        ->args([
            service('payum.dynamic_gateways.cypher'),
        ])
    ;

    $services
        ->set('commerce_weavers_sylius_tpay.form.event_listener.encrypt_gateway_config', EncryptGatewayConfigListener::class)
        ->args([
            service('payum.dynamic_gateways.cypher'),
        ])
    ;

    $services->set('commerce_weavers_sylius_tpay.form.event_listener.remove_unnecessary_payment_details_fields', RemoveUnnecessaryPaymentDetailsFieldsListener::class);

    $services
        ->set('commerce_weavers_sylius_tpay.form.event_listener.add_saved_credit_cards', AddSavedCreditCardsListener::class)
        ->deprecate('commerce-weavers/sylius-tpay-plugin', '2.0', 'The "%service_id%" service is deprecated and will be removed in the version 2.0 as it is considered daed code.')
        ->args([
            service('security.token_storage'),
            service('translator'),
            service('commerce_weavers_sylius_tpay.repository.credit_card'),
        ])
    ;
};

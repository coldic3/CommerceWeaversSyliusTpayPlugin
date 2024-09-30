<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateBlik0PaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateBlik0PaymentPayloadFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateCardPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateCardPaymentPayloadFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\BasicPaymentFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\X509Factory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\X509FactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\CertificateResolver;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\CertificateResolverInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\TrustedCertificateResolver;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\TrustedCertificateResolverInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifier;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifierInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\SignatureVerifier;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\SignatureVerifierInterface;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_tpay.tpay.factory.create_blik0_payment_payload', CreateBlik0PaymentPayloadFactory::class)
        ->args([
            service('commerce_weavers_tpay.tpay.factory.create_redirect_based_payment_payload'),
        ])
        ->alias(CreateBlik0PaymentPayloadFactoryInterface::class, 'commerce_weavers_tpay.factory.create_blik0_payment_payload')
    ;

    $services->set('commerce_weavers_tpay.tpay.factory.create_card_payment_payload', CreateCardPaymentPayloadFactory::class)
        ->args([
            service('commerce_weavers_tpay.tpay.factory.create_redirect_based_payment_payload'),
        ])
        ->alias(CreateCardPaymentPayloadFactoryInterface::class, 'commerce_weavers_tpay.factory.create_card_payment_payload')
    ;

    $services->set('commerce_weavers_tpay.tpay.factory.create_redirect_based_payment_payload', CreateRedirectBasedPaymentPayloadFactory::class)
        ->args([
            service('router'),
            param('commerce_weavers_tpay.payum.create_transaction.success_route'),
            param('commerce_weavers_tpay.payum.create_transaction.error_route'),
        ])
        ->alias(CreateRedirectBasedPaymentPayloadFactoryInterface::class, 'commerce_weavers_tpay.factory.create_redirect_based_payment_payload')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.factory.basic_payment', BasicPaymentFactory::class)
        ->alias(BasicPaymentFactory::class, 'commerce_weavers_tpay.security.notification.factory.basic_payment')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.factory.x509', X509Factory::class)
        ->alias(X509FactoryInterface::class, 'commerce_weavers_tpay.security.notification.factory.x509')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.resolver.certificate', CertificateResolver::class)
        ->alias(CertificateResolverInterface::class, 'commerce_weavers_tpay.security.notification.resolver.certificate')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.resolver.trusted_certificate', TrustedCertificateResolver::class)
        ->alias(TrustedCertificateResolverInterface::class, 'commerce_weavers_tpay.security.notification.resolver.trusted_certificate')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.verifier.checksum', ChecksumVerifier::class)
        ->alias(ChecksumVerifierInterface::class, 'commerce_weavers_tpay.security.notification.verifier.checksum')
    ;

    $services->set('commerce_weavers_tpay.tpay.security.notification.verifier.signature', SignatureVerifier::class)
        ->args([
            service('commerce_weavers_tpay.tpay.security.notification.resolver.certificate'),
            service('commerce_weavers_tpay.tpay.security.notification.resolver.trusted_certificate'),
            service('commerce_weavers_tpay.tpay.security.notification.factory.x509'),
        ])
        ->alias(SignatureVerifierInterface::class, 'commerce_weavers_tpay.security.notification.verifier.signature')
    ;
};

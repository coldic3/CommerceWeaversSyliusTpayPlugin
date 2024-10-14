<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Payment\Canceller\PaymentCanceller;
use CommerceWeavers\SyliusTpayPlugin\Payment\Canceller\PaymentCancellerInterface;
use CommerceWeavers\SyliusTpayPlugin\Payment\Checker\PaymentCancellationPossibilityChecker;
use CommerceWeavers\SyliusTpayPlugin\Payment\Checker\PaymentCancellationPossibilityCheckerInterface;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.payment.canceller.payment', PaymentCanceller::class)
        ->args([
            service('sylius_abstraction.state_machine')->nullOnInvalid(),
            service('sm.factory'),
        ])
        ->alias(PaymentCancellerInterface::class, 'commerce_weavers_sylius_tpay.payment.canceller.payment')
    ;

    $services->set('commerce_weavers_sylius_tpay.payment.checker.payment_cancellation_possibility', PaymentCancellationPossibilityChecker::class)
        ->args([
            service('sylius_abstraction.state_machine')->nullOnInvalid(),
            service('sm.factory'),
        ])
        ->alias(PaymentCancellationPossibilityCheckerInterface::class, 'commerce_weavers_sylius_tpay.payment.checker.payment_cancellation_possibility')
    ;
};

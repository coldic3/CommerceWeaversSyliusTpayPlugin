<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\GooglePayPayment\Validator\Constraint\EncodedGooglePayTokenValidator;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.google_pay_payment.validator.constraint.encoded_google_pay_token', EncodedGooglePayTokenValidator::class)
        ->tag('validator.constraint_validator')
    ;
};

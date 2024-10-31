<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Api\OpenApi\PayDocumentationModifier;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.api.open_api.pay_documentation_modifier', PayDocumentationModifier::class)
        ->args([
            param('sylius.security.new_api_shop_route')
        ])
        ->tag('sylius.open_api.modifier')
    ;
};

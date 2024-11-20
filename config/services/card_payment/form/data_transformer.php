<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Form\DataTransformer\CardTypeDataTransformer;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.card_payment.form.data_transformer.card_type', CardTypeDataTransformer::class);
};

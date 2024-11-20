<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\EventListener\AddCreditCardToAccountMenuEventListener;
use Sylius\Bundle\ShopBundle\Menu\AccountMenuBuilder;

return function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('commerce_weavers_sylius_tpay.card_payment.event_listener.add_credit_card_to_account_menu', AddCreditCardToAccountMenuEventListener::class)
        ->tag('kernel.event_listener', ['event' => AccountMenuBuilder::EVENT_NAME, 'method' => '__invoke'])
    ;
};

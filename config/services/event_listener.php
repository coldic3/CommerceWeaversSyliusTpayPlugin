<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\ContextProvider\BankListContextProvider;
use CommerceWeavers\SyliusTpayPlugin\ContextProvider\RegulationsUrlContextProvider;
use CommerceWeavers\SyliusTpayPlugin\EventListener\AddCreditCardToAccountMenuEventListener;
use Sylius\Bundle\ShopBundle\Menu\AccountMenuBuilder;

return static function(ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(AddCreditCardToAccountMenuEventListener::class)
        ->tag('kernel.event_listener', ['event' => AccountMenuBuilder::EVENT_NAME, 'method' => '__invoke'])

    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\DisplayWaitingForPaymentPage;
use CommerceWeavers\SyliusTpayPlugin\Routing;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Routing::SHOP_WAITING_FOR_PAYMENT, Routing::SHOP_WAITING_FOR_PAYMENT_PATH)
        ->controller(DisplayWaitingForPaymentPage::class)
        ->methods([Request::METHOD_GET])
    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\DisplayWaitingForPaymentPage;
use CommerceWeavers\SyliusTpayPlugin\Route;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Route::SHOP_WAITING_FOR_PAYMENT, Route::SHOP_WAITING_FOR_PAYMENT_PATH)
        ->controller(DisplayWaitingForPaymentPage::class)
        ->methods([Request::METHOD_GET])
    ;
};

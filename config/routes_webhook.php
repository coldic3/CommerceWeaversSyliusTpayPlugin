<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\PaymentNotificationAction;
use CommerceWeavers\SyliusTpayPlugin\Route;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Route::WEBHOOK_PAYMENT_NOTIFICATION, Route::WEBHOOK_PAYMENT_NOTIFICATION_PATH)
        ->controller(PaymentNotificationAction::class)
        ->methods([Request::METHOD_POST])
    ;
};

<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\PaymentNotificationAction;
use CommerceWeavers\SyliusTpayPlugin\Routing;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Routing::WEBHOOK_PAYMENT_NOTIFICATION, Routing::WEBHOOK_PAYMENT_NOTIFICATION_PATH)
        ->controller(PaymentNotificationAction::class)
        ->methods([Request::METHOD_POST])
    ;
};

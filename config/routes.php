<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\PaymentNotificationAction;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add('commerce_weavers_tpay_payment_notification', '/{_locale}/order/tpay/payment-notification')
        ->controller(PaymentNotificationAction::class)
        ->methods([Request::METHOD_POST])
    ;
};

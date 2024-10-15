<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\DisplayPaymentFailedPageAction;
use CommerceWeavers\SyliusTpayPlugin\Controller\DisplayThankYouPageAction;
use CommerceWeavers\SyliusTpayPlugin\Controller\DisplayWaitingForPaymentPage;
use CommerceWeavers\SyliusTpayPlugin\Controller\RetryPaymentAction;
use CommerceWeavers\SyliusTpayPlugin\Routing;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Routing::SHOP_PAYMENT_FAILED, Routing::SHOP_PAYMENT_FAILED_PATH)
        ->controller(DisplayPaymentFailedPageAction::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add(Routing::SHOP_RETRY_PAYMENT, Routing::SHOP_RETRY_PAYMENT_PATH)
        ->controller(RetryPaymentAction::class)
        ->methods([Request::METHOD_POST])
    ;

    $routes->add(Routing::SHOP_THANK_YOU, Routing::SHOP_THANK_YOU_PATH)
        ->controller(DisplayThankYouPageAction::class)
        ->methods([Request::METHOD_GET])
    ;

    $routes->add(Routing::SHOP_WAITING_FOR_PAYMENT, Routing::SHOP_WAITING_FOR_PAYMENT_PATH)
        ->controller(DisplayWaitingForPaymentPage::class)
        ->methods([Request::METHOD_GET])
    ;
};

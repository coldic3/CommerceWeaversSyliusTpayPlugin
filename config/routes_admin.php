<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

use CommerceWeavers\SyliusTpayPlugin\Controller\TpayGetChannelsAction;
use CommerceWeavers\SyliusTpayPlugin\Routing;
use Symfony\Component\HttpFoundation\Request;

return function(RoutingConfigurator $routes): void {
    $routes->add(Routing::ADMIN_TPAY_CHANNELS, Routing::ADMIN_TPAY_CHANNELS_PATH)
        ->controller(TpayGetChannelsAction::class)
        ->methods([Request::METHOD_GET])
    ;
};

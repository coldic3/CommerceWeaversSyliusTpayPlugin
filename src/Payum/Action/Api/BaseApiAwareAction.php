<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Tpay\OpenApi\Api\TpayApi;
use Tpay\OpenApi\Utilities\Logger;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = TpayApi::class;
        Logger::setLogPath(dirname(__DIR__, 4) . '/tests/Application/var/log/');
    }
}

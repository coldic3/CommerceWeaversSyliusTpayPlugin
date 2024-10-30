<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Model;

use Payum\Core\Model\GatewayConfigInterface as BaseGatewayConfigInterface;
use Payum\Core\Security\CryptedInterface;

interface GatewayConfigInterface extends BaseGatewayConfigInterface, CryptedInterface
{
}

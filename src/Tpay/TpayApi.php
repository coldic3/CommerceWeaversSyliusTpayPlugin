<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay;

use Tpay\OpenApi\Api\TpayApi as BaseTpayApi;

class TpayApi extends BaseTpayApi
{
    public function __construct(
        $clientId,
        $clientSecret,
        $productionMode = false,
        $scope = 'read',
        $apiUrlOverride = null,
        $clientName = null,
        private readonly ?string $notificationSecretCode = null,
    ) {
        parent::__construct($clientId, $clientSecret, $productionMode, $scope, $apiUrlOverride, $clientName);
    }

    public function getNotificationSecretCode(): ?string
    {
        return $this->notificationSecretCode;
    }
}

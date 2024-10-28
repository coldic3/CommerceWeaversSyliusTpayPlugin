<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use Payum\Core\Request\Generic;

class InitializeApplePayPayment extends Generic
{
    public function __construct(
        mixed $model,
        private readonly string $domainName,
        private readonly string $displayName,
        private readonly string $validationUrl,
    ) {
        parent::__construct($model);
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getValidationUrl(): string
    {
        return $this->validationUrl;
    }
}

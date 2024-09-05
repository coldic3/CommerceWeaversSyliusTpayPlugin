<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use Payum\Core\Request\Generic;

class CreateBlik0Transaction extends Generic
{
    public function __construct (
        private string $afterUrl,
        mixed $model,
    ) {
        parent::__construct($model);
    }

    public function getAfterUrl(): string
    {
        return $this->afterUrl;
    }
}

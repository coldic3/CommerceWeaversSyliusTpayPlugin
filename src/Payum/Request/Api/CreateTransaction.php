<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use Payum\Core\Request\Generic;

class CreateTransaction extends Generic
{
    public function __construct(
        mixed $model,
    ) {
        parent::__construct($model);
    }
}

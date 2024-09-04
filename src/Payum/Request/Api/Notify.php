<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api;

use ArrayAccess;
use Payum\Core\Request\Notify as BaseNotify;

class Notify extends BaseNotify
{
    public function __construct (
        mixed $model,
        private \ArrayAccess $data,
    ) {
        parent::__construct($model);
    }

    public function getData(): ArrayAccess
    {
        return $this->data;
    }
}

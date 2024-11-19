<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\BlikPayment\PreconditionGuard\Exception;

class BlikAliasNotRegisteredException extends \Exception
{
    public function __construct(
        string $message = 'The Blik Alias is not registered yet. Please try again later or use a Blik token.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

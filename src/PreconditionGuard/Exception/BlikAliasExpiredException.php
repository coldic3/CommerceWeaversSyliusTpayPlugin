<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PreconditionGuard\Exception;

class BlikAliasExpiredException extends \Exception
{
    public function __construct(
        string $message = 'The Blik Alias expired. Please register a new one or use a Blik token.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

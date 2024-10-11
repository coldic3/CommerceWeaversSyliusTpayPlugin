<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payment\Exception;

use Sylius\Component\Core\Model\PaymentInterface;

class PaymentCannotBeCancelledException extends \RuntimeException
{
    public function __construct(
        PaymentInterface $payment,
        string $message = 'Payment with id "%d" cannot be cancelled.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $message = sprintf($message, $payment->getId());

        parent::__construct($message, $code, $previous);
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\Refund;
use Sylius\Component\Core\Model\PaymentInterface;

final class RefundAction implements ActionInterface
{
    /**
     * @param Refund $request
     */
    public function execute(mixed $request): void
    {
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Refund && $request->getModel() instanceof PaymentInterface;
    }
}

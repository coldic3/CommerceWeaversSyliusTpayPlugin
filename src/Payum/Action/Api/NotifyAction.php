<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Sylius\Component\Payment\Model\PaymentInterface;

final class NotifyAction extends BaseApiAwareAction implements GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Notify $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();
        $details = $model->getDetails();

        /** @var array{tr_status?: string} $notificationData */
        $notificationData = $request->getData();
        $status = $notificationData['tr_status'] ?? '';

        $details['tpay']['status'] = match(true) {
            str_contains($status, 'TRUE') => PaymentInterface::STATE_COMPLETED,
            str_contains($status, 'CHARGEBACK') => PaymentInterface::STATE_REFUNDED,
            default => PaymentInterface::STATE_FAILED,
        };

        $model->setDetails($details);
    }

    public function supports($request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof PaymentInterface;
    }
}

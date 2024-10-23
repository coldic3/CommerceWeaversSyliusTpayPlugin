<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\BasicPaymentFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifierInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\SignatureVerifierInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Generic;
use Sylius\Component\Core\Model\PaymentInterface;

final class NotifyAction extends BasePaymentAwareAction implements GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function __construct(
        private readonly BasicPaymentFactoryInterface $basicPaymentFactory,
        private readonly ChecksumVerifierInterface $checksumVerifier,
        private readonly SignatureVerifierInterface $signatureVerifier,
    ) {
        parent::__construct();
    }

    /**
     * @param Notify $request
     */
    protected function doExecute(Generic $request, PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        $requestData = $request->getData();

        $paymentData = $this->basicPaymentFactory->createFromArray($requestData->requestParameters);
        $isChecksumValid = $this->checksumVerifier->verify(
            $paymentData,
            $this->api->getNotificationSecretCode() ?? throw new \RuntimeException('Notification secret code is not set'),
        );
        $isSignatureValid = $this->signatureVerifier->verify($requestData->jws, $requestData->requestContent);

        if (!$isChecksumValid) {
            throw new HttpResponse('FALSE - Invalid checksum', 400);
        }

        if (!$isSignatureValid) {
            throw new HttpResponse('FALSE - Invalid signature', 400);
        }

        /** @var string $status */
        $status = $paymentData->tr_status;

        $newPaymentStatus = match (true) {
            str_contains($status, 'TRUE') => PaymentInterface::STATE_COMPLETED,
            str_contains($status, 'CHARGEBACK') => PaymentInterface::STATE_REFUNDED,
            default => PaymentInterface::STATE_FAILED,
        };

        $paymentDetails->setStatus($newPaymentStatus);
    }

    public function supports($request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof PaymentInterface;
    }
}

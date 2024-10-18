<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\Notify;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\NotifyAliasRegister;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\NotifyAliasUnregister;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\NotifyTransaction;
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

        if (!$this->signatureVerifier->verify($requestData->jws, $requestData->requestContent)) {
            throw new HttpResponse('FALSE - Invalid signature', 400);
        }

        try {
            /** @var array $requestContent */
            $requestContent = json_decode($requestData->requestContent, true, flags: \JSON_THROW_ON_ERROR);

            // FIXME: Refactor me, please! I'm a temporary solution to handle different types of notifications.
            if ('ALIAS_REGISTER' === ($requestContent['event'] ?? null)) {
                $this->gateway->execute(new NotifyAliasRegister($model, $requestData));
            } elseif (in_array($requestContent['event'] ?? null, ['ALIAS_UNREGISTER', 'ALIAS_EXPIRED'], true)) {
                $this->gateway->execute(new NotifyAliasUnregister($model, $requestData));
            }
        } catch (\JsonException) {
            $this->gateway->execute(new NotifyTransaction($model, $requestData));
        }
    }

    public function supports($request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof PaymentInterface;
    }
}

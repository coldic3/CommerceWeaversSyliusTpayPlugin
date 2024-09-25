<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateRedirectBasedPaymentPayloadFactoryInterface;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;

class CreateRedirectBasedTransactionAction extends AbstractCreateTransactionAction
{
    use GenericTokenFactoryAwareTrait;

    public function __construct(
        private CreateRedirectBasedPaymentPayloadFactoryInterface $createRedirectBasedPaymentPayloadFactory,
        private NotifyTokenFactoryInterface $notifyTokenFactory,
    ) {
        parent::__construct();
    }

    /**
     * @param CreateTransaction $request
     */
    public function execute($request): void
    {
        /** @var PaymentInterface $model */
        $model = $request->getModel();
        $gatewayName = $request->getToken()?->getGatewayName() ?? $this->getGatewayNameFrom($model);

        $localeCode = $this->getLocaleCodeFrom($model);
        $notifyToken = $this->notifyTokenFactory->create($model, $gatewayName, $localeCode);

        $response = $this->api->transactions()->createTransaction(
            $this->createRedirectBasedPaymentPayloadFactory->createFrom($model, $notifyToken->getTargetUrl(), $localeCode),
        );

        $details = $model->getDetails();
        $details['tpay']['transaction_id'] = $response['transactionId'];
        $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];

        $model->setDetails($details);

        throw new HttpRedirect($details['tpay']['transaction_payment_url']);
    }

    public function supports($request): bool
    {
        if (!$request instanceof CreateTransaction) {
            return false;
        }

        $model = $request->getModel();

        if (!$model instanceof PaymentInterface) {
            return false;
        }

        $details = $model->getDetails();

        return !isset($details['tpay']['card']) && !isset($details['tpay']['blik_token']);
    }
}

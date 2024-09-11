<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\CreateTransaction;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateBlik0PaymentPayloadFactoryInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Tpay\OpenApi\Api\TpayApi;
use Webmozart\Assert\Assert;

/**
 * @property TpayApi $api
 */
final class CreateBlik0TransactionAction extends AbstractCreateTransactionAction
{
    use GenericTokenFactoryAwareTrait;

    public function __construct(
        private CreateBlik0PaymentPayloadFactoryInterface $createBlik0PaymentPayloadFactory,
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
        $token = $request->getToken();
        Assert::notNull($token);

        $localeCode = $this->getLocaleCodeFrom($model);
        $notifyToken = $this->notifyTokenFactory->create($model, $token->getGatewayName(), $localeCode);

        $response = $this->api->transactions()->createTransaction(
            $this->createBlik0PaymentPayloadFactory->createFrom($model, $notifyToken->getTargetUrl(), $localeCode),
        );

        $details = $model->getDetails();
        $details['tpay']['transaction_id'] = $response['transactionId'];
        $details['tpay']['status'] = $response['status'];

        $model->setDetails($details);
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

        return isset($details['tpay']['blik_token']);
    }
}

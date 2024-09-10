<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\CreateTransactionFactoryInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function __construct(
        private CreateTransactionFactoryInterface $createTransactionFactory,
        private CreateTransactionFactoryInterface $createBlik0TransactionFactory,
    ) {
    }

    /**
     * @param Capture $request
     */
    public function execute($request): void
    {
        $token = $request->getToken();
        Assert::notNull($token);

        /** @var PaymentInterface $model */
        $model = $request->getModel();

        if ($this->transactionIsBlik($model)) {
            $this->gateway->execute(
                $this->createBlik0TransactionFactory->createNewWithModel($token),
            );

            return;
        }

        $this->gateway->execute(
            $this->createTransactionFactory->createNewWithModel($token),
        );

        throw new HttpRedirect($token->getAfterUrl());
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }

    private function transactionIsBlik(PaymentInterface $model): bool
    {
        return array_key_exists('tpay', $model->getDetails()) &&
            array_key_exists('blik', $model->getDetails()['tpay'])
        ;
    }
}

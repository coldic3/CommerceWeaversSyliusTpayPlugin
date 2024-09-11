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
    ) {
    }

    /**
     * @param Capture $request
     */
    public function execute($request): void
    {
        $token = $request->getToken();
        Assert::notNull($token);

        $this->gateway->execute(
            $this->createTransactionFactory->createNewWithModel($token),
        );

        throw new HttpRedirect($token->getAfterUrl());
    }

    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use Payum\Core\GatewayAwareTrait;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

abstract class AbstractCreateTransactionAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;
    use GatewayAwareTrait;

    public function __construct(
        private RouterInterface $router,
    ) {
        parent::__construct();
    }

    protected function createTransaction(PaymentInterface $payment, array $payload): void
    {
        $details = $payment->getDetails();

        $order = $payment->getOrder();
        Assert::notNull($order);
        $localeCode = $order->getLocaleCode();
        Assert::notNull($localeCode);

        $response = $this->api->transactions()->createTransaction($payload);

        $details['tpay']['transaction_id'] = $response['transactionId'];
        $details['tpay']['transaction_payment_url'] = $response['transactionPaymentUrl'];

        $payment->setDetails($details);
    }

    protected function getLocaleCodeFrom(PaymentInterface $payment): string
    {
        return $payment->getOrder()->getLocaleCode() ?? throw new \InvalidArgumentException('Cannot determine locale code for a given payment');
    }
}

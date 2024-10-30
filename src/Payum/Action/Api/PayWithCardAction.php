<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\PayWithCard;
use CommerceWeavers\SyliusTpayPlugin\Repository\CreditCardRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Generic;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

class PayWithCardAction extends BasePaymentAwareAction
{
    public function __construct(private readonly CreditCardRepositoryInterface $creditCardRepository)
    {
        parent::__construct();
    }

    protected function doExecute(Generic $request, PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        Assert::notNull($paymentDetails->getEncodedCardData(), 'Card data is required to pay with card.');
        Assert::notNull($paymentDetails->getTransactionId(), 'Transaction ID is required to pay with card.');

        $payload = $this->getPayload($paymentDetails);

        $this->do(
            fn () => $this->api->transactions()->createPaymentByTransactionId($payload, $paymentDetails->getTransactionId()),
            onSuccess: function ($response) use ($paymentDetails) {
                $paymentDetails->setResult($response['result']);
                $paymentDetails->setStatus($response['status']);
                $paymentDetails->setPaymentUrl($response['transactionPaymentUrl'] ?? null);
            },
            onFailure: fn () => $paymentDetails->setStatus(PaymentInterface::STATE_FAILED),
        );

        $paymentDetails->clearSensitiveData();
    }

    protected function postExecute(PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        if ($paymentDetails->getPaymentUrl() !== null && $paymentDetails->getPaymentUrl() !== '') {
            throw new HttpRedirect($paymentDetails->getPaymentUrl());
        }
    }

    public function supports($request): bool
    {
        return $request instanceof PayWithCard && $request->getModel() instanceof PaymentInterface;
    }

    /**
     * @param PaymentDetails $paymentDetails
     *
     * @return array
     */
    private function getPayload(PaymentDetails $paymentDetails): array
    {
        $payload = [
            'groupId' => PayGroup::CARD,
        ];

        if ($paymentDetails->getUseSavedCreditCard() !== null) {
            $payload['cardPaymentData'] = [
                'token' => $this->creditCardRepository->find($paymentDetails->getUseSavedCreditCard())->getToken(),
            ];

            return $payload;
        }

        $payload['cardPaymentData'] = [
            'card' => $paymentDetails->getEncodedCardData(),
        ];

        if ($paymentDetails->isSaveCreditCardForLater()) {
            $payload['cardPaymentData']['save'] = true;
        }

        return $payload;
    }
}

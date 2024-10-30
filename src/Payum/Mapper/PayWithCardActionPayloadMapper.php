<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api\BasePaymentAwareAction;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\PayWithCard;
use CommerceWeavers\SyliusTpayPlugin\Repository\CreditCardRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Generic;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

class PayWithCardActionPayloadMapper implements PayWithCardActionPayloadMapperInterface
{
    public function __construct(private readonly CreditCardRepositoryInterface $creditCardRepository)
    {
    }

    /**
     * @param PaymentDetails $paymentDetails
     *
     * @return array<'groupId' => string, 'cardPaymentData' => array>
     */
    public function getPayload(PaymentDetails $paymentDetails): array
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

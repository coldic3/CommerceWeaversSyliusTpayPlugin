<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Repository\CreditCardRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Webmozart\Assert\Assert;

class PayWithCardActionPayloadMapper implements PayWithCardActionPayloadMapperInterface
{
    public function __construct(private readonly CreditCardRepositoryInterface $creditCardRepository)
    {
    }

    /**
     * @return array{'groupId': int, 'cardPaymentData': array}
     */
    public function getPayload(PaymentDetails $paymentDetails): array
    {
        $payload = [
            'groupId' => PayGroup::CARD,
        ];

        if ($paymentDetails->getUseSavedCreditCard() !== null) {
            /** @var CreditCardInterface|null $creditCard */
            $creditCard = $this->creditCardRepository->find($paymentDetails->getUseSavedCreditCard());

            Assert::notNull($creditCard);

            $payload['cardPaymentData'] = [
                'token' => $creditCard->getToken(),
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

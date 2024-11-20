<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Payum\Mapper;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Repository\CreditCardRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

class PayWithCardActionPayloadMapper implements PayWithCardActionPayloadMapperInterface
{
    public function __construct(private readonly CreditCardRepositoryInterface $creditCardRepository)
    {
    }

    /**
     * @return array{'groupId': int, 'cardPaymentData': array}
     */
    public function getPayload(PaymentDetails $paymentDetails, PaymentInterface $payment): array
    {
        $payload = [
            'groupId' => PayGroup::CARD,
        ];

        if ($paymentDetails->getUseSavedCreditCard() !== null) {
            /** @var OrderInterface $order */
            $order = $payment->getOrder();
            /** @var ChannelInterface $channel */
            $channel = $order->getChannel();
            /** @var CustomerInterface $customer */
            $customer = $order->getCustomer();

            /** @var CreditCardInterface|null $creditCard */
            $creditCard = $this->creditCardRepository->findOneByIdCustomerAndChannel($paymentDetails->getUseSavedCreditCard(), $customer, $channel);

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

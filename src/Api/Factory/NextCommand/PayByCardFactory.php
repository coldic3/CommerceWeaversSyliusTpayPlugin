<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommand;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayByCard;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class PayByCardFactory implements NextCommandFactoryInterface
{
    public function create(Pay $command, PaymentInterface $payment): PayByCard
    {
        if (!$this->supports($command, $payment)) {
            throw new UnsupportedNextCommandFactory('This factory does not support the given command.');
        }

        /** @var int $paymentId */
        $paymentId = $payment->getId();
        /** @var string $encodedCardData */
        $encodedCardData = $command->encodedCardData;

        $saveCard = $command->saveCard;

        return new PayByCard($paymentId, $encodedCardData, $saveCard ?? false);
    }

    public function supports(Pay $command, PaymentInterface $payment): bool
    {
        return $command->encodedCardData !== null && $payment->getId() !== null;
    }
}

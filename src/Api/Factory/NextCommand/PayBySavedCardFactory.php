<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommand;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Command\PayBySavedCard;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class PayBySavedCardFactory implements NextCommandFactoryInterface
{
    public function create(Pay $command, PaymentInterface $payment): PayBySavedCard
    {
        if (!$this->supports($command, $payment)) {
            throw new UnsupportedNextCommandFactory('This factory does not support the given command.');
        }

        /** @var int $paymentId */
        $paymentId = $payment->getId();
        /** @var string $savedCardId */
        $savedCardId = $command->savedCardId;

        return new PayBySavedCard($paymentId, $savedCardId);
    }

    public function supports(Pay $command, PaymentInterface $payment): bool
    {
        return $command->savedCardId !== null && $payment->getId() !== null;
    }
}

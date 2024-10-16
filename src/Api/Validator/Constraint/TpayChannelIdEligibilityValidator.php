<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Api\Resource\TpayChannel;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class TpayChannelIdEligibilityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $tpayTransactionChannelResolver,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, TpayChannelIdEligibility::class);

        if (null === $value) {
            return;
        }

        Assert::string($value);

        $apiChannels = $this->tpayTransactionChannelResolver->resolve();

        $channel = null;

        if (array_key_exists($value, $apiChannels)) {
            $channel = TpayChannel::fromArray($apiChannels[$value]);
        }

        if (null === $channel) {
            $this->context->buildViolation($constraint->doesNotExistMessage)
                ->setCode($constraint::TPAY_CHANNEL_ID_DOES_NOT_EXIST_ERROR)
                ->addViolation()
            ;

            return;
        }

        if (false === $channel->getAvailable()) {
            $this->context->buildViolation($constraint->availableMessage)
                ->setCode($constraint::TPAY_CHANNEL_ID_AVAILABLE_ERROR)
                ->addViolation()
            ;

            return;
        }

        if (
            true === $channel->getInstantRedirection() &&
            true === $channel->getOnlinePayment()
        ) {
            return;
        }

        $this->context->buildViolation($constraint->notBankMessage)
            ->setCode($constraint::TPAY_CHANNEL_ID_NOT_BANK_ERROR)
            ->addViolation()
        ;
    }
}

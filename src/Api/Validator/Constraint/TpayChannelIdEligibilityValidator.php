<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Resource\TpayChannel;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class TpayChannelIdEligibilityValidator extends ConstraintValidator
{
    public const TPAY_CHANNEL_ID_FIELD_NAME = 'tpayChannelId';

    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $tpayTransactionChannelResolver,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, Pay::class);
        Assert::isInstanceOf($constraint, TpayChannelIdEligibility::class);

        if (null === $value->tpayChannelId) {
            return;
        }

        $channelId = $value->tpayChannelId;

        $apiChannels = $this->tpayTransactionChannelResolver->resolve();

        $channel = null;

        if (array_key_exists($channelId, $apiChannels)) {
            $channel = TpayChannel::fromArray($apiChannels[$channelId]);
        }

        if (null === $channel) {
            $this->context->buildViolation($constraint->doesNotExistMessage)
                ->atPath(self::TPAY_CHANNEL_ID_FIELD_NAME)
                ->setCode($constraint::TPAY_CHANNEL_ID_AVAILABLE_ERROR)
                ->addViolation()
            ;

            return;
        }

        if (false === $channel->getAvailable()) {
            $this->context->buildViolation($constraint->availableMessage)
                ->atPath(self::TPAY_CHANNEL_ID_FIELD_NAME)
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
            ->atPath(self::TPAY_CHANNEL_ID_FIELD_NAME)
            ->setCode($constraint::TPAY_CHANNEL_ID_AVAILABLE_ERROR)
            ->addViolation()
        ;
    }
}

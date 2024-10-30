<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidTpayApiChannelValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ValidTpayChannelListProviderInterface $validatedTpayApiBankListProvider,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        $validChannels = $this->validatedTpayApiBankListProvider->provide();

        if (array_key_exists($value, $validChannels)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setCode($constraint::NOT_VALID_CHANNEL_ERROR)
            ->addViolation()
        ;
    }
}

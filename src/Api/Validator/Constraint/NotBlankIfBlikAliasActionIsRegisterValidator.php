<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Api\Enum\BlikAliasAction;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class NotBlankIfBlikAliasActionIsRegisterValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotBlankIfBlikAliasActionIsRegister) {
            throw new UnexpectedTypeException($constraint, NotBlankIfBlikAliasActionIsRegister::class);
        }

        if (null === $constraint->blikAliasActionPropertyName) {
            throw new MissingOptionsException(
                sprintf('Option "blikAliasActionPropertyName" must be given for constraint "%s".', __CLASS__),
                ['blikAliasActionPropertyName'],
            );
        }

        $contextObject = $this->context->getObject();
        if (null === $contextObject) {
            throw new UnexpectedTypeException($contextObject, 'object');
        }

        if (null !== $value && (!is_string($value) || '' !== trim($value))) {
            return;
        }

        $propertyAccessor = new PropertyAccessor();
        $blikAliasAction = $propertyAccessor->getValue($contextObject, $constraint->blikAliasActionPropertyName);

        if ($blikAliasAction !== BlikAliasAction::REGISTER) {
            return;
        }

        $this->context
            ->buildViolation($constraint->fieldRequiredErrorMessage)
            ->setCode($constraint::FIELD_REQUIRED_ERROR)
            ->addViolation()
        ;
    }
}

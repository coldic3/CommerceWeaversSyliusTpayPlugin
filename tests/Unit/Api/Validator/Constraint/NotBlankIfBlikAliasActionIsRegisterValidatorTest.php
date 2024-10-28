<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\Api\Enum\BlikAliasAction;
use CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint\NotBlankIfBlikAliasActionIsRegister;
use CommerceWeavers\SyliusTpayPlugin\Api\Validator\Constraint\NotBlankIfBlikAliasActionIsRegisterValidator;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class NotBlankIfBlikAliasActionIsRegisterValidatorTest extends ConstraintValidatorTestCase
{
    use ProphecyTrait;

    public function test_it_throws_an_exception_if_a_constraint_has_an_invalid_type(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(
            'somevalue',
            $this->prophesize(Constraint::class)->reveal(),
        );
    }

    public function test_it_throws_an_exception_if_a_constraint_has_not_blik_alias_action_property_name_specified(): void
    {
        $this->expectException(MissingOptionsException::class);

        $this->setObject(null);

        $this->validator->validate(
            'somevalue',
            new NotBlankIfBlikAliasActionIsRegister(),
        );
    }

    public function test_it_throws_an_exception_if_a_context_object_is_null(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate(
            'somevalue',
            new NotBlankIfBlikAliasActionIsRegister(blikAliasActionPropertyName: 'aliasAction'),
        );
    }

    /** @dataProvider notEmptyDataProvider */
    public function test_it_does_not_validate_if_value_is_not_empty(mixed $value): void
    {
        $this->setObject(new \stdClass());

        $this->validator->validate(
            $value,
            new NotBlankIfBlikAliasActionIsRegister(blikAliasActionPropertyName: 'aliasAction'),
        );

        $this->assertNoViolation();
    }

    public function test_it_does_not_validate_if_blik_alias_action_is_not_register(): void
    {
        $this->setObject((object) ['aliasAction' => BlikAliasAction::APPLY]);

        $this->validator->validate(
            '',
            new NotBlankIfBlikAliasActionIsRegister(blikAliasActionPropertyName: 'aliasAction'),
        );

        $this->assertNoViolation();
    }

    public function test_it_builds_violation_if_value_is_empty_and_blik_alias_action_is_register(): void
    {
        $this->setObject((object) ['aliasAction' => BlikAliasAction::REGISTER]);

        $this->validator->validate(
            '',
            new NotBlankIfBlikAliasActionIsRegister(blikAliasActionPropertyName: 'aliasAction'),
        );

        $this
            ->buildViolation('commerce_weavers_sylius_tpay.shop.pay.field.not_blank')
            ->setCode(NotBlankIfBlikAliasActionIsRegister::FIELD_REQUIRED_ERROR)
            ->assertRaised()
        ;
    }

    protected function createValidator(): NotBlankIfBlikAliasActionIsRegisterValidator
    {
        return new NotBlankIfBlikAliasActionIsRegisterValidator();
    }

    private function notEmptyDataProvider(): array
    {
        return [
            [['i_am_an_array_element']],
            ['i_am_a_string'],
        ];
    }
}

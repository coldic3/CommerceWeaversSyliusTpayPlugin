<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\PayByLinkPayment\Validator\Constraint;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Validator\Constraint\ValidTpayChannel;
use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Validator\Constraint\ValidTpayChannelValidator;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use InvalidArgumentException;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class ValidTpayChannelValidatorTest extends ConstraintValidatorTestCase
{
    use ProphecyTrait;

    private const EXAMPLE_PROVIDE = [
        '1' => [
            'id' => '1',
            'name' => 'some bank',
            'available' => true,
            'groups' => [
                ['id' => '1'],
            ],
        ],
        '2' => [
            'id' => '2',
            'name' => 'card payment',
            'available' => true,
            'groups' => [
                ['id' => '103'],
            ],
        ],
    ];

    private ValidTpayChannelListProviderInterface|ObjectProphecy $validTpayChannelListProvider;

    protected function setUp(): void
    {
        $this->validTpayChannelListProvider = $this->prophesize(ValidTpayChannelListProviderInterface::class);

        parent::setUp();
    }

    public function test_it_throws_an_exception_if_a_value_is_not_a_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(new \stdClass(), new ValidTpayChannel());
    }

    public function test_it_throws_an_exception_if_a_value_has_an_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(1, new ValidTpayChannel());
    }

    public function test_it_throws_an_exception_if_a_constraint_has_an_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(
            '11',
            $this->prophesize(Constraint::class)->reveal(),
        );
    }

    public function test_it_builds_violation_if_tpay_channel_id_is_not_valid(): void
    {
        $this->validTpayChannelListProvider->provide()->willReturn(self::EXAMPLE_PROVIDE);

        $this->validator->validate(
            '111',
            new ValidTpayChannel(),
        );

        $this->buildViolation('commerce_weavers_sylius_tpay.shop.pay.tpay_channel.not_valid')
            ->setCode('632f97f3-c302-409b-a321-ec078194302d')
            ->assertRaised()
        ;
    }

    protected function createValidator(): ValidTpayChannelValidator
    {
        return new ValidTpayChannelValidator($this->validTpayChannelListProvider->reveal());
    }
}

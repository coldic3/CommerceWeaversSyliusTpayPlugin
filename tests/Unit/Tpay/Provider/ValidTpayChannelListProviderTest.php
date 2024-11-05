<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\UnableToGetBankListException;
use CommerceWeavers\SyliusTpayPlugin\Repository\PaymentMethodRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\AvailableTpayChannelListProviderInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProvider;
use Payum\Core\Security\CypherInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class ValidTpayChannelListProviderTest extends TestCase
{
    use ProphecyTrait;

    private const BANK_LIST = [
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
        '3' => [
            'id' => '3',
            'name' => 'BLIK',
            'available' => true,
            'groups' => [
                ['id' => '150'],
            ],
        ],
        '4' => [
            'id' => '4',
            'name' => 'Apple Pay',
            'available' => true,
            'groups' => [
                ['id' => '170'],
            ],
        ],
        '5' => [
            'id' => '5',
            'name' => 'Google Pay',
            'available' => true,
            'groups' => [
                ['id' => '166'],
            ],
        ],
        '6' => [
            'id' => '6',
            'name' => 'Visa mobile',
            'available' => true,
            'groups' => [
                ['id' => '171'],
            ],
        ],
        '7' => [
            'id' => '7',
            'name' => 'Visa mobile on site',
            'available' => true,
            'groups' => [
                ['id' => '177'],
            ],
        ],
    ];

    private AvailableTpayChannelListProviderInterface|ObjectProphecy $availableTpayApiBankListProvider;

    private PaymentMethodRepositoryInterface|ObjectProphecy $paymentMethodRepository;

    private ChannelContextInterface|ObjectProphecy $channelContext;

    private CypherInterface|ObjectProphecy $cypher;

    protected function setUp(): void
    {
        $this->availableTpayApiBankListProvider = $this->prophesize(AvailableTpayChannelListProviderInterface::class);
        $this->paymentMethodRepository = $this->prophesize(PaymentMethodRepositoryInterface::class);
        $this->channelContext = $this->prophesize(ChannelContextInterface::class);
        $this->cypher = $this->prophesize(CypherInterface::class);
    }

    public function test_it_throws_exception_if_there_is_no_tpay_based_payment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no payment method of Tpay type available');

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $channel = $this->prophesize(ChannelInterface::class);
        $this->channelContext->getChannel()->willReturn($channel->reveal());

        $this->paymentMethodRepository->findByChannelAndGatewayConfigNameWithGatewayConfig(
            $channel,
            'tpay'
        )->willReturn([]);

        $this->createTestSubject()->provide();
    }

    public function test_it_throws_exception_if_there_is_no_gateway_config_that_is_pbl_type(): void
    {
        $this->expectException(UnableToGetBankListException::class);
        $this->expectExceptionMessage('Bank list cannot be retrieved if there is no payment method with PayByLink type configured');

        $notTpayBasedPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $notTpayBasedGatewayConfig = $this->prophesize(GatewayConfig::class);
        $notTpayBasedGatewayConfigConfig = [
            'type' => 'visa_mobile',
        ];
        $notTpayBasedPaymentMethod->getGatewayConfig()->willReturn($notTpayBasedGatewayConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $channel = $this->prophesize(ChannelInterface::class);
        $this->channelContext->getChannel()->willReturn($channel->reveal());

        $notTpayBasedGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $notTpayBasedGatewayConfig->getConfig()->willReturn($notTpayBasedGatewayConfigConfig);

        $this->paymentMethodRepository
            ->findByChannelAndGatewayConfigNameWithGatewayConfig($channel, 'tpay')
            ->willReturn([
                $notTpayBasedPaymentMethod
            ])
        ;

        $this->createTestSubject()->provide();
    }

    public function test_it_returns_all_available_payments_if_only_tpay_payment_method_is_pbl(): void
    {
        $tpayBasedPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfig::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayBasedPaymentMethod->getGatewayConfig()->willReturn($tpayPblGatewayConfig);

        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $channel = $this->prophesize(ChannelInterface::class);
        $this->channelContext->getChannel()->willReturn($channel->reveal());

        $this->paymentMethodRepository
            ->findByChannelAndGatewayConfigNameWithGatewayConfig($channel, 'tpay')
            ->willReturn([
                $tpayBasedPaymentMethod->reveal(),
            ])
        ;

        $result = $this->createTestSubject()->provide();

        $this->assertSame(self::BANK_LIST, $result);
    }

    public function test_it_returns_valid_payments_according_to_available_tpay_payment_methods(): void
    {
        $tpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfig::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblPaymentMethod->getGatewayConfig()->willReturn($tpayPblGatewayConfig);
        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $anotherTpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $anotherTpayPblGatewayConfig = $this->prophesize(GatewayConfig::class);
        $anotherTpayPblGatewayConfigConfig = [
            'type' => 'visa_mobile',
        ];
        $anotherTpayPblPaymentMethod->getGatewayConfig()->willReturn($anotherTpayPblGatewayConfig);
        $anotherTpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $anotherTpayPblGatewayConfig->getConfig()->willReturn($anotherTpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $channel = $this->prophesize(ChannelInterface::class);
        $this->channelContext->getChannel()->willReturn($channel->reveal());

        $this->paymentMethodRepository
            ->findByChannelAndGatewayConfigNameWithGatewayConfig($channel, 'tpay')
            ->willReturn([
                $tpayPblPaymentMethod->reveal(),
                $anotherTpayPblPaymentMethod->reveal(),
            ])
        ;

        $result = $this->createTestSubject()->provide();

        $expected = self::BANK_LIST;
        // unsets both methods for visa mobile and its onsite version
        unset($expected['6'], $expected['7']);

        $this->assertSame($expected, $result);
    }

    public function test_it_returns_valid_payments_even_if_gateway_config_lacks_type(): void
    {
        $tpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfig::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblPaymentMethod->getGatewayConfig()->willReturn($tpayPblGatewayConfig);
        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $anotherTpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $anotherTpayPblGatewayConfig = $this->prophesize(GatewayConfig::class);
        $anotherTpayPblGatewayConfigConfig = [
            'i_have_no_type' => 'i_should_still_work',
        ];
        $anotherTpayPblPaymentMethod->getGatewayConfig()->willReturn($anotherTpayPblGatewayConfig);
        $anotherTpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $anotherTpayPblGatewayConfig->getConfig()->willReturn($anotherTpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $channel = $this->prophesize(ChannelInterface::class);
        $this->channelContext->getChannel()->willReturn($channel->reveal());

        $this->paymentMethodRepository
            ->findByChannelAndGatewayConfigNameWithGatewayConfig($channel, 'tpay')
            ->willReturn([
                $tpayPblPaymentMethod->reveal(),
                $anotherTpayPblPaymentMethod->reveal(),
            ])
        ;

        $result = $this->createTestSubject()->provide();

        $this->assertSame(self::BANK_LIST, $result);
    }

    private function createTestSubject(): ValidTpayChannelListProvider
    {
        return new ValidTpayChannelListProvider(
            $this->availableTpayApiBankListProvider->reveal(),
            $this->paymentMethodRepository->reveal(),
            $this->channelContext->reveal(),
            $this->cypher->reveal(),
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Model\GatewayConfigInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\UnableToGetBankListException;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\AvailableTpayChannelListProviderInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProvider;
use Payum\Core\Security\CypherInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
    ];

    private AvailableTpayChannelListProviderInterface|ObjectProphecy $availableTpayApiBankListProvider;

    private RepositoryInterface|ObjectProphecy $gatewayMethodRepository;

    private CypherInterface|ObjectProphecy $cypher;

    protected function setUp(): void
    {
        $this->availableTpayApiBankListProvider = $this->prophesize(AvailableTpayChannelListProviderInterface::class);
        $this->gatewayMethodRepository = $this->prophesize(RepositoryInterface::class);
        $this->cypher = $this->prophesize(CypherInterface::class);
    }

    public function test_it_throws_exception_if_there_is_no_tpay_based_payment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no gateway config of Tpay type available');

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->gatewayMethodRepository
            ->findBy(['gatewayName' => 'tpay'])
            ->willReturn([])
        ;

        $this->createTestSubject()->provide();
    }

    public function test_it_throws_exception_if_there_is_no_gateway_config_that_is_pbl_type(): void
    {
        $this->expectException(UnableToGetBankListException::class);
        $this->expectExceptionMessage('Bank list cannot be retrieved if there is no payment method with PayByLink type configured');

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $notPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $notPblGatewayConfigConfig = [
            'type' => 'visa_mobile',
        ];
        $notPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $notPblGatewayConfig->getConfig()->willReturn($notPblGatewayConfigConfig);

        $this->gatewayMethodRepository
            ->findBy(['gatewayName' => 'tpay'])
            ->willReturn([$notPblGatewayConfig->reveal()])
        ;

        $this->createTestSubject()->provide();
    }

    public function test_it_returns_all_available_payments_if_only_tpay_payment_method_is_pbl(): void
    {
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldNotBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->gatewayMethodRepository
            ->findBy(['gatewayName' => 'tpay'])
            ->willReturn([$tpayPblGatewayConfig->reveal()])
        ;

        $result = $this->createTestSubject()->provide();

        $this->assertSame(self::BANK_LIST, $result);
    }

    public function test_it_returns_valid_payments_according_to_available_tpay_payment_methods(): void
    {
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $anotherTpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $anotherTpayPblGatewayConfigConfig = [
            'type' => 'visa_mobile',
        ];
        $anotherTpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $anotherTpayPblGatewayConfig->getConfig()->willReturn($anotherTpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->gatewayMethodRepository
            ->findBy(['gatewayName' => 'tpay'])
            ->willReturn([
                $tpayPblGatewayConfig->reveal(),
                $anotherTpayPblGatewayConfig->reveal(),
            ])
        ;

        $result = $this->createTestSubject()->provide();

        $expected = self::BANK_LIST;
        unset($expected['6']);

        $this->assertSame($expected, $result);
    }

    public function test_it_returns_valid_payments_even_if_gateway_config_lacks_type(): void
    {
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);

        $anotherTpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $anotherTpayPblGatewayConfigConfig = [
            'i_have_no_type' => 'i_should_still_work',
        ];
        $anotherTpayPblGatewayConfig->decrypt($this->cypher)->shouldBeCalled();
        $anotherTpayPblGatewayConfig->getConfig()->willReturn($anotherTpayPblGatewayConfigConfig);

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->gatewayMethodRepository
            ->findBy(['gatewayName' => 'tpay'])
            ->willReturn([
                $tpayPblGatewayConfig->reveal(),
                $anotherTpayPblGatewayConfig->reveal(),
            ])
        ;

        $result = $this->createTestSubject()->provide();

        $this->assertSame(self::BANK_LIST, $result);
    }

    private function createTestSubject(): ValidTpayChannelListProvider
    {
        return new ValidTpayChannelListProvider(
            $this->availableTpayApiBankListProvider->reveal(),
            $this->gatewayMethodRepository->reveal(),
            $this->cypher->reveal(),
        );
    }
}

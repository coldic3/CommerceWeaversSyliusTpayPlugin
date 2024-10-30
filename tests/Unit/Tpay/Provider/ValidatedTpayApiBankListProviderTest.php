<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\AvailableTpayChannelListProviderInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Payum\Core\Model\GatewayConfigInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ValidatedTpayApiBankListProviderTest extends TestCase
{
    use ProphecyTrait;

    private const BANK_LIST = [
        '1' => [
            'id' => '1',
            'name' => 'some bank',
            'available' => true,
        ],
        '103' => [
            'id' => '103',
            'name' => 'card payment',
            'available' => true,
        ],
        '150' => [
            'id' => '150',
            'name' => 'BLIK',
            'available' => true,
        ],
        '170' => [
            'id' => '170',
            'name' => 'Apple Pay',
            'available' => true,
        ],
        '166' => [
            'id' => '166',
            'name' => 'Google Pay',
            'available' => true,
        ],
        '171' => [
            'id' => '171',
            'name' => 'Visa mobile',
            'available' => true,
        ],
    ];

    private AvailableTpayChannelListProviderInterface|ObjectProphecy $availableTpayApiBankListProvider;

    private RepositoryInterface|ObjectProphecy $paymentMethodRepository;

    protected function setUp(): void
    {
        $this->availableTpayApiBankListProvider = $this->prophesize(AvailableTpayChannelListProviderInterface::class);
        $this->paymentMethodRepository = $this->prophesize(RepositoryInterface::class);
    }

    public function test_it_throws_exception_if_there_is_no_tpay_based_payment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no payment of Tpay type available');

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->paymentMethodRepository
            ->findBy(['gatewayConfig.gateway_name' => 'tpay'])
            ->willReturn(new ArrayCollection([]))
        ;

        $this->createTestSubject()->provide();
    }

    public function test_it_returns_all_available_payments_if_only_tpay_payment_method_is_pbl(): void
    {
        $tpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);
        $tpayPblPaymentMethod->getGatewayConfig()->willReturn($tpayPblGatewayConfig->reveal());

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->paymentMethodRepository
            ->findBy(['gatewayConfig.gateway_name' => 'tpay'])
            ->willReturn(new ArrayCollection([$tpayPblPaymentMethod->reveal()]))
        ;

        $result = $this->createTestSubject()->provide();

        $this->assertSame(self::BANK_LIST, $result);
    }

    public function test_it_returns_valid_payments_according_to_available_tpay_payment_methods(): void
    {
        $tpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $tpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $tpayPblGatewayConfigConfig = [
            'type' => 'pay_by_link',
        ];
        $tpayPblGatewayConfig->getConfig()->willReturn($tpayPblGatewayConfigConfig);
        $tpayPblPaymentMethod->getGatewayConfig()->willReturn($tpayPblGatewayConfig->reveal());

        $anotherTpayPblPaymentMethod = $this->prophesize(PaymentMethodInterface::class);
        $anotherTpayPblGatewayConfig = $this->prophesize(GatewayConfigInterface::class);
        $anotherTpayPblGatewayConfigConfig = [
            'type' => 'visa_mobile',
        ];
        $anotherTpayPblGatewayConfig->getConfig()->willReturn($anotherTpayPblGatewayConfigConfig);
        $anotherTpayPblPaymentMethod->getGatewayConfig()->willReturn($anotherTpayPblGatewayConfig->reveal());

        $this->availableTpayApiBankListProvider->provide()->willReturn(self::BANK_LIST);

        $this->paymentMethodRepository
            ->findBy(['gatewayConfig.gateway_name' => 'tpay'])
            ->willReturn(new ArrayCollection([
                $tpayPblPaymentMethod->reveal(),
                $anotherTpayPblPaymentMethod->reveal(),
            ]))
        ;

        $result = $this->createTestSubject()->provide();

        $expected = self::BANK_LIST;
        unset($expected['171']);

        $this->assertSame($expected, $result);
    }

    private function createTestSubject(): ValidTpayChannelListProvider
    {
        return new ValidTpayChannelListProvider(
            $this->availableTpayApiBankListProvider->reveal(),
            $this->paymentMethodRepository->reveal(),
        );
    }
}

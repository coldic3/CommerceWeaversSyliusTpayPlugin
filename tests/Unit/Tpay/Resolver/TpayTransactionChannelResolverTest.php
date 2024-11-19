<?php

declare(strict_types=1);


namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Resolver;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Factory\GetTpayTransactionsChannelsFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolver;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Tpay\OpenApi\Utilities\TpayException;

final class TpayTransactionChannelResolverTest extends TestCase
{
    use ProphecyTrait;

    private Payum|ObjectProphecy $payum;

    private GetTpayTransactionsChannelsFactoryInterface|ObjectProphecy $getTpayTransactionsChannelsFactory;

    private LoggerInterface|ObjectProphecy $logger;

    protected function setUp(): void
    {
        $this->payum = $this->prophesize(Payum::class);
        $this->getTpayTransactionsChannelsFactory = $this->prophesize(GetTpayTransactionsChannelsFactoryInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    public function test_it_resolves_tpay_transaction_channels(): void
    {
        $gateway = $this->prophesize(GatewayInterface::class);
        $value = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->payum->getGateway('tpay')->willReturn($gateway);
        $this->getTpayTransactionsChannelsFactory->createNewEmpty()->willReturn($value);
        $gateway->execute($value, true)->shouldBeCalled();
        $value->getResult()->willReturn([
            'result' => 'success',
            'channels' => [
                ['id' => 1, 'name' => 'Bank 1'],
                ['id' => 2, 'name' => 'Bank 2'],
            ],
        ]);

        $result = $this->createTestSubject()->resolve();

        $this->assertEquals([
            1 => ['id' => 1, 'name' => 'Bank 1'],
            2 => ['id' => 2, 'name' => 'Bank 2'],
        ], $result);
    }

    public function test_it_resolves_an_empty_array_when_tpay_exception_is_thrown(): void
    {
        $gateway = $this->prophesize(GatewayInterface::class);
        $value = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->payum->getGateway('tpay')->willReturn($gateway);
        $this->getTpayTransactionsChannelsFactory->createNewEmpty()->willReturn($value);
        $gateway
            ->execute($value, true)
            ->willThrow(new TpayException('Booo! I am a TpayException!'))
        ;
        $this->logger
            ->critical('Unable to get banks list. TpayException thrown.', Argument::withKey('exceptionMessage'))
            ->shouldBeCalled()
        ;

        $result = $this->createTestSubject()->resolve();

        $this->assertEquals([], $result);
    }

    public function test_it_resolves_an_empty_array_when_the_result_is_not_success(): void
    {
        $gateway = $this->prophesize(GatewayInterface::class);
        $value = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->payum->getGateway('tpay')->willReturn($gateway);
        $value->getResult()->willReturn(['result' => 'failure']);
        $this->getTpayTransactionsChannelsFactory->createNewEmpty()->willReturn($value);
        $gateway->execute($value, true)->shouldBeCalled();

        $this->logger
            ->critical('Unable to get banks list. The result is not success.', ['responseBody' => '{"result":"failure"}'])
            ->shouldBeCalled()
        ;

        $result = $this->createTestSubject()->resolve();

        $this->assertEquals([], $result);
    }

    public function test_it_resolves_an_empty_array_when_the_channels_key_is_missing(): void
    {
        $gateway = $this->prophesize(GatewayInterface::class);
        $value = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->payum->getGateway('tpay')->willReturn($gateway);
        $value->getResult()->willReturn(['result' => 'success']);
        $this->getTpayTransactionsChannelsFactory->createNewEmpty()->willReturn($value);
        $gateway->execute($value, true)->shouldBeCalled();

        $this->logger
            ->critical('Unable to get banks list. The channels key is missing.', ['responseBody' => '{"result":"success"}'])
            ->shouldBeCalled()
        ;

        $result = $this->createTestSubject()->resolve();

        $this->assertEquals([], $result);
    }

    public function test_it_does_not_log_errors_if_logger_is_null(): void
    {
        $gateway = $this->prophesize(GatewayInterface::class);
        $value = $this->prophesize(GetTpayTransactionsChannels::class);
        $this->payum->getGateway('tpay')->willReturn($gateway);
        $value->getResult()->willReturn(['result' => 'failure']);
        $this->getTpayTransactionsChannelsFactory->createNewEmpty()->willReturn($value);
        $gateway->execute($value, true)->shouldBeCalled();

        $this->logger->critical(Argument::cetera())->shouldNotBeCalled();

        $result = $this->createTestSubject(false)->resolve();

        $this->assertEquals([], $result);
    }

    private function createTestSubject(bool $withLogger = true): TpayTransactionChannelResolver
    {
        return new TpayTransactionChannelResolver(
            $this->payum->reveal(),
            $this->getTpayTransactionsChannelsFactory->reveal(),
            $withLogger ? $this->logger->reveal() : null,
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Api\Factory;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnresolvableNextCommandException;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactory;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\NextCommandFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sylius\Component\Core\Model\Payment;

final class NextCommandFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_throws_an_exception_when_none_command_factory_is_supported(): void
    {
        $this->expectException(UnresolvableNextCommandException::class);
        $this->expectExceptionMessage('No valid next command found.');

        $command = new Pay('token');
        $payment = new Payment();

        $someFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $someFactory->supports($command, $payment)->willReturn(false);

        $anotherFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $anotherFactory->supports($command, $payment)->willReturn(false);

        $nextCommandFactories = [
            $someFactory->reveal(),
            $anotherFactory->reveal(),
        ];

        $this->createTestSubject($nextCommandFactories)->create($command, $payment);
    }

    public function test_it_throws_an_exception_when_more_than_one_command_factory_is_supported(): void
    {
        $this->expectException(UnresolvableNextCommandException::class);
        $this->expectExceptionMessage('Multiple valid next commands found.');

        $command = new Pay('token');
        $payment = new Payment();

        $someFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $someFactory->supports($command, $payment)->willReturn(true);
        $someFactory->create($command, $payment)->willReturn(new \stdClass());

        $anotherFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $anotherFactory->supports($command, $payment)->willReturn(true);
        $anotherFactory->create($command, $payment)->willReturn(new \stdClass());

        $nextCommandFactories = [
            $someFactory->reveal(),
            $anotherFactory->reveal(),
        ];

        $this->createTestSubject($nextCommandFactories)->create($command, $payment);
    }

    public function test_it_returns_a_factored_command(): void
    {
        $command = new Pay('token');
        $payment = new Payment();

        $someFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $someFactory->supports($command, $payment)->willReturn(false);
        $someFactory->create($command, $payment)->willReturn(new \stdClass());

        $anotherFactory = $this->prophesize(NextCommandFactoryInterface::class);
        $anotherFactory->supports($command, $payment)->willReturn(true);
        $anotherFactory->create($command, $payment)->willReturn($expectedCommand = new \stdClass());

        $nextCommandFactories = [
            $someFactory->reveal(),
            $anotherFactory->reveal(),
        ];

        $actualCommand = $this->createTestSubject($nextCommandFactories)->create($command, $payment);

        $this->assertSame($expectedCommand, $actualCommand);
    }

    private function createTestSubject(iterable $nextCommandFactories): NextCommandFactoryInterface
    {
        return new NextCommandFactory($nextCommandFactories);
    }
}

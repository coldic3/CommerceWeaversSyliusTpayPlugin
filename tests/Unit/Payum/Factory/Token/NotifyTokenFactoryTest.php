<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Factory\Token;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactory;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\Token\NotifyTokenFactoryInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class NotifyTokenFactoryTest extends TestCase
{
    use ProphecyTrait;

    private Payum|ObjectProphecy $payum;

    private TokenFactoryInterface|ObjectProphecy $tokenFactory;

    private RouterInterface|ObjectProphecy $router;

    protected function setUp(): void
    {
        $this->payum = $this->prophesize(Payum::class);
        $this->tokenFactory = $this->prophesize(TokenFactoryInterface::class);
        $this->router = $this->prophesize(RouterInterface::class);

        $this->payum->getTokenFactory()->willReturn($this->tokenFactory);
    }

    public function test_it_returns_created_notify_token(): void
    {
        $payment = $this->prophesize(PaymentInterface::class);

        $this->router
            ->generate('cw_notify', ['_locale' => 'pl_PL'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://cw.org/notify')
        ;

        $this->tokenFactory->createToken(
            'tpay',
            $payment,
            'https://cw.org/notify',
        )->shouldBeCalled()->willReturn($this->prophesize(TokenInterface::class));

        $this->createTestSubject()->create($payment->reveal(), 'tpay', 'pl_PL');
    }

    private function createTestSubject(): NotifyTokenFactoryInterface
    {
        return new NotifyTokenFactory(
            $this->payum->reveal(),
            $this->router->reveal(),
            'cw_notify',
        );
    }
}

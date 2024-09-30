<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Security\Notification\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\X509Factory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\X509FactoryInterface;
use PHPUnit\Framework\TestCase;
use Tpay\OpenApi\Utilities\phpseclib\File\X509;

final class X509FactoryTest extends TestCase
{
    public function test_it_creates_a_fresh_x509_instance(): void
    {
        $x509 = $this->createTestSubject()->create();

        $this->assertInstanceOf(X509::class, $x509);
    }

    private function createTestSubject(): X509FactoryInterface
    {
        return new X509Factory();
    }
}

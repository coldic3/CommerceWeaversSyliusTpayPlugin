<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Security\Notification\Checker;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Checker\ProductionModeChecker;
use PHPUnit\Framework\TestCase;

final class ProductionModeCheckerTest extends TestCase
{
    /** @dataProvider productionX5uCertificateDataProvider */
    public function test_it_checks_that_x5u_is_a_production_certificate(string $x5u): void
    {
        $isProduction = (new ProductionModeChecker())->isProduction($x5u);

        $this->assertTrue($isProduction);
    }

    /** @dataProvider sandboxX5uCertificateDataProvider */
    public function test_it_checks_that_x5u_is_not_a_production_certificate(string $x5u): void
    {
        $isProduction = (new ProductionModeChecker())->isProduction($x5u);

        $this->assertFalse($isProduction);
    }

    private function productionX5uCertificateDataProvider(): array
    {
        return [
            ['https://secure.tpay.com/cert/cert.crt'],
            ['http://secure.tpay.com/hello'],
            ['http://secure.tpay.com'],
        ];
    }

    private function sandboxX5uCertificateDataProvider(): array
    {
        return [
            ['https://secure.sandbox.tpay.com/cert/cert.crt'],
            ['http://secure.sandbox.tpay.com/hello'],
            ['http://secure.sandbox.tpay.com'],
            ['i_am_not_a_url'],
            [''],
        ];
    }
}

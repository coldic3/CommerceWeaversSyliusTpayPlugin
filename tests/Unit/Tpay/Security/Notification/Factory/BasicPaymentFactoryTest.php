<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Security\Notification\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\BasicPaymentFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\BasicPaymentFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class BasicPaymentFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_create_a_basic_payment_object_from_a_request(): void
    {
        $paymentData = $this->createTestSubject()->createFromArray([
            'id' => '94016',
            'tr_id' => 'TR-4FRM-1UBMYX',
            'tr_date' => '2024-09-25 13:12:57',
            'tr_crc' => '',
            'tr_amount' => '10.68',
            'tr_paid' => '10.68',
            'tr_desc' => 'zamówienie #000000028',
            'tr_status' => 'TRUE',
            'tr_error' => 'none',
            'tr_email' => 'john.doe@example.com',
            'test_mode' => '0',
            'md5sum' => 'ea24e29d12b274f459e4adbed17687a3',
        ]);

        $this->assertSame(94016, $paymentData->id);
        $this->assertSame('TR-4FRM-1UBMYX', $paymentData->tr_id);
        $this->assertSame('2024-09-25 13:12:57', $paymentData->tr_date);
        $this->assertSame('', $paymentData->tr_crc);
        $this->assertSame(10.68, $paymentData->tr_amount);
        $this->assertSame(10.68, $paymentData->tr_paid);
        $this->assertSame('zamówienie #000000028', $paymentData->tr_desc);
        $this->assertSame('TRUE', $paymentData->tr_status);
        $this->assertSame('none', $paymentData->tr_error);
        $this->assertSame('john.doe@example.com', $paymentData->tr_email);
        $this->assertSame(0, $paymentData->test_mode);
        $this->assertSame('ea24e29d12b274f459e4adbed17687a3', $paymentData->md5sum);
    }

    private function createTestSubject(): BasicPaymentFactoryInterface
    {
        return new BasicPaymentFactory();
    }
}

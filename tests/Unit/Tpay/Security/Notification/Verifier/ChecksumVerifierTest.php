<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Security\Notification\Verifier;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifier;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\ChecksumVerifierInterface;
use PHPUnit\Framework\TestCase;
use Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment;

final class ChecksumVerifierTest extends TestCase
{
    /**
     * @dataProvider data_provider_it_returns_whether_a_calculated_checksum_matches_the_expected_checksum
     */
    public function test_it_returns_whether_a_calculated_checksum_matches_the_expected_checksum(
        BasicPayment $paymentData,
        string $merchantSecret,
        bool $expectedResult,
    ): void {
        $checksumVerifier = $this->createTestSubject();

        $this->assertSame($expectedResult, $checksumVerifier->verify($paymentData, $merchantSecret));
    }

    public function data_provider_it_returns_whether_a_calculated_checksum_matches_the_expected_checksum(): iterable
    {
        $paymentData = new BasicPayment();
        $paymentData->id = 1;
        $paymentData->tr_id = 2;
        $paymentData->tr_amount = 3.0;
        $paymentData->tr_crc = '4';
        $paymentData->md5sum = 'a8debd2916d4e28f2849d63ae2783d04'; // it is precalculated checksum for given data

        yield 'payment data with a correct checksum' => [
            'paymentData' => $paymentData,
            'merchantSecret' => 'secret',
            'expectedResult' => true,
        ];

        $paymentData = clone $paymentData;
        $paymentData->md5sum = 'invalid_checksum';

        yield 'payment data with an incorrect checksum' => [
            'paymentData' => $paymentData,
            'merchantSecret' => 'secret',
            'expectedResult' => false,
        ];

        yield 'payment data with a correct checksum, but a different merchant secret' => [
            'paymentData' => $paymentData,
            'merchantSecret' => 'different',
            'expectedResult' => false,
        ];
    }

    private function createTestSubject(): ChecksumVerifierInterface
    {
        return new ChecksumVerifier();
    }
}

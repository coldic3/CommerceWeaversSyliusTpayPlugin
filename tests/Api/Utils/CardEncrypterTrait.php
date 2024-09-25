<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils;

trait CardEncrypterTrait
{
    private function encryptCardData(string $number, \DateTimeInterface $expirationDate, string $cvc): string
    {
        $number = str_replace(' ', '', $number);
        $expirationDate = $expirationDate->format('m/y');
        $host = 'https://commerceweavers.com';

        openssl_public_encrypt(
            join('|', [$number, $expirationDate, $cvc, $host]),
            $encryptedData,
            base64_decode(getenv('TPAY_CARDS_API')),
        );

        return base64_encode($encryptedData);
    }
}

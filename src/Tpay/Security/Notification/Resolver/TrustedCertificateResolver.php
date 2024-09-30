<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver;

final class TrustedCertificateResolver implements TrustedCertificateResolverInterface
{
    private const TPAY_PRODUCTION_URL = 'https://secure.tpay.com';

    private const TPAY_SANDBOX_URL = 'https://secure.sandbox.tpay.com';

    public function resolve(bool $production = false): string
    {
        $url = sprintf(
            '%s/x509/tpay-jws-root.pem',
            $production ? self::TPAY_PRODUCTION_URL : self::TPAY_SANDBOX_URL,
        );

        $ch = curl_init();
        curl_setopt(
            $ch,
            \CURLOPT_URL,
            $url,
        );
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        if (!is_string($result)) {
            throw new \RuntimeException('Failed to fetch trusted certificate from URL: ' . $url);
        }

        return $result;
    }
}

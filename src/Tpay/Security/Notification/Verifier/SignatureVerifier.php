<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Checker\ProductionModeCheckerInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory\X509FactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\CertificateResolverInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Resolver\TrustedCertificateResolverInterface;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Verifier\Exception\InvalidSignatureException;
use Tpay\OpenApi\Utilities\phpseclib\Crypt\RSA;

final class SignatureVerifier implements SignatureVerifierInterface
{
    public function __construct(
        private readonly CertificateResolverInterface $certificateResolver,
        private readonly TrustedCertificateResolverInterface $trustedCertificateResolver,
        private readonly X509FactoryInterface $x509Factory,
        private readonly ?ProductionModeCheckerInterface $productionModeChecker = null,
    ) {
        if (null === $this->productionModeChecker) {
            trigger_deprecation(
                'commerce-weavers/sylius-tpay-plugin',
                '1.0',
                'Not passing a $productionModeChecker to %s constructor is deprecated and will be removed in SyliusTpayPlugin 2.0.',
                self::class,
            );
        }
    }

    public function verify(string $jws, string $requestContent): bool
    {
        $jwsData = explode('.', $jws);
        $headers = $jwsData[0] ?? null;
        $signature = $jwsData[2] ?? null;

        if (null === $headers || null === $signature) {
            throw new InvalidSignatureException('Invalid JWS format');
        }

        /** @var string $headersJson */
        $headersJson = base64_decode(strtr($headers, '-_', '+/'), true);

        /** @var array $headersData */
        $headersData = json_decode($headersJson, true);

        /** @var string|null $x5u */
        $x5u = $headersData['x5u'] ?? null;

        if (null === $x5u) {
            throw new InvalidSignatureException('Missing x5u header');
        }

        $production = $this->productionModeChecker?->isProduction($x5u) ?? false;
        $certificate = $this->certificateResolver->resolve($x5u);
        $trusted = $this->trustedCertificateResolver->resolve($production);

        $x509 = $this->x509Factory->create();
        $x509->loadX509($certificate);
        $x509->loadCA($trusted);

        if (true !== $x509->validateSignature()) {
            return false;
        }

        $payload = str_replace('=', '', strtr(base64_encode($requestContent), '+/', '-_'));
        $decodedSignature = base64_decode(strtr($signature, '-_', '+/'), true);
        $publicKey = $x509->getPublicKey();
        $publicKey = $x509->withSettings($publicKey, 'sha256', RSA::SIGNATURE_PKCS1);

        return $publicKey->verify(sprintf('%s.%s', $headers, $payload), $decodedSignature);
    }
}

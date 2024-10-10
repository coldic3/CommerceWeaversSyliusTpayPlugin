<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Controller;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class RetryPaymentAction
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request, string $orderToken): Response
    {
        /** @var string|null $csrfToken */
        $csrfToken = $request->request->get('_csrf_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($orderToken, $csrfToken))) {
            throw new BadRequestException('Invalid CSRF token');
        }

        return new Response('Retry payment');
    }
}

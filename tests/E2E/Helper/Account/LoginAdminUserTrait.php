<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Account;

use Symfony\Component\Panther\Client;

/**
 * @property Client $client
 */
trait LoginAdminUserTrait
{
    protected function loginAdminUser(string $email, string $password): void
    {
        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Login', ['_username' => $email, '_password' => $password]);
    }
}

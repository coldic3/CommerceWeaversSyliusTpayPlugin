<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];
}

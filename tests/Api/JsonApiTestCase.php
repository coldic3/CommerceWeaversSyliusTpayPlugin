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
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/json'];

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    protected function assertResponseViolations(Response $response, array $expectedViolations): void
    {
        $this->assertResponseCode($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonHeader($response);
        $this->assertJsonResponseViolations($response, $expectedViolations);
    }

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    protected function assertJsonResponseViolations(
        Response $response,
        array $expectedViolations,
        bool $assertViolationsCount = true,
    ): void {
        $responseContent = $response->getContent() ?: '';
        $this->assertNotEmpty($responseContent);
        $violations = json_decode($responseContent, true)['violations'] ?? [];

        if ($assertViolationsCount) {
            $this->assertCount(count($expectedViolations), $violations, $responseContent);
        }

        $violationMap = [];
        foreach ($violations as $violation) {
            $violationMap[$violation['propertyPath']][] = $violation['message'];
        }

        foreach ($expectedViolations as $expectedViolation) {
            $propertyPath = $expectedViolation['propertyPath'];
            $this->assertArrayHasKey($propertyPath, $violationMap, $responseContent);
            $this->assertContains($expectedViolation['message'], $violationMap[$propertyPath], $responseContent);
        }
    }
}

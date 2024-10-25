<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Exception;

final class BlikAliasAmbiguousValueException extends AbstractBadRequestHttpException
{
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array<array{applicationName: string, applicationCode: string}> $applications
     */
    public static function create(array $applications): self
    {
        $exception = new self();

        $exception->message = (string) json_encode([
            'description' => 'Too many aliases found for a Blik alias. Specify one of the applications.',
            'applications' => $applications,
        ]);

        return $exception;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\BlikPayment\Payum\Exception;

class BlikAliasAmbiguousValueException extends \Exception
{
    /** @var array<array{applicationName: string, applicationCode: string}> */
    private array $applications = [];

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

        $exception->message = 'Too many aliases found for a Blik alias. Specify one of the applications.';
        $exception->applications = $applications;

        return $exception;
    }

    /**
     * @return array<array{applicationName: string, applicationCode: string}>
     */
    public function getApplications(): array
    {
        return $this->applications;
    }
}

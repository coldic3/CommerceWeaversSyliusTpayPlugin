<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Payum\Factory;

use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\NotifyDataFactory;
use CommerceWeavers\SyliusTpayPlugin\Payum\Factory\NotifyDataFactoryInterface;
use PHPUnit\Framework\TestCase;

final class NotifyDataFactoryTest extends TestCase
{
    public function test_it_creates_a_notify_data_service(): void
    {
        $notifyData = $this->createTestSubject()->create('signature', 'content', ['request']);

        $this->assertSame('signature', $notifyData->jws);
        $this->assertSame('content', $notifyData->requestContent);
        $this->assertSame(['request'], $notifyData->requestParameters);
    }

    private function createTestSubject(): NotifyDataFactoryInterface
    {
        return new NotifyDataFactory();
    }
}

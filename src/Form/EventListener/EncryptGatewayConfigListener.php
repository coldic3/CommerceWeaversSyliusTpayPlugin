<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\EventListener;

use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;

final class EncryptGatewayConfigListener
{
    public function __construct(
        private CypherInterface $cypher,
    ) {
    }

    public function __invoke(PostSubmitEvent $event): void
    {
        $gatewayConfig = $event->getData();

        if (!$gatewayConfig instanceof CryptedInterface) {
            return;
        }

        $gatewayConfig->encrypt($this->cypher);

        $event->setData($gatewayConfig);
    }
}

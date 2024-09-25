<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Factory;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnresolvableNextCommandException;
use CommerceWeavers\SyliusTpayPlugin\Api\Factory\Exception\UnsupportedNextCommandFactory;
use Sylius\Component\Core\Model\PaymentInterface;

final class NextCommandFactory implements NextCommandFactoryInterface
{
    /**
     * @param iterable<NextCommandFactoryInterface> $nextCommandFactories
     */
    public function __construct(
        private readonly iterable $nextCommandFactories,
    ) {
    }

    public function create(Pay $command, PaymentInterface $payment): object
    {
        $factoredCommands = [];

        foreach ($this->nextCommandFactories as $nextCommandFactory) {
            if (!$nextCommandFactory->supports($command, $payment)) {
                continue;
            }

            try {
                $factoredCommand = $nextCommandFactory->create($command, $payment);
            } catch (UnsupportedNextCommandFactory) {
                continue;
            }

            $factoredCommands[] = $factoredCommand;
        }

        if (count($factoredCommands) === 0) {
            throw new UnresolvableNextCommandException('No valid next command found.');
        }

        if (count($factoredCommands) > 1) {
            throw new UnresolvableNextCommandException('Multiple valid next commands found.');
        }

        return array_pop($factoredCommands);
    }

    public function supports(Pay $command, PaymentInterface $payment): bool
    {
        return true;
    }
}

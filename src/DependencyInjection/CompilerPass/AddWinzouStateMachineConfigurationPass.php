<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

final class AddWinzouStateMachineConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            /** @var string $stateMachineDefaultAdapter */
            $stateMachineDefaultAdapter = $container->resolveEnvPlaceholders(
                $container->getParameter('sylius_abstraction.state_machine.default_adapter'),
                true,
            );
            /** @var array<string, string> $stateMachineAdapterMapping */
            $stateMachineAdapterMapping = $container->resolveEnvPlaceholders(
                $container->getParameter('sylius_abstraction.state_machine.graphs_to_adapters_mapping'),
                true,
            );
        } catch (ParameterNotFoundException) {
            return;
        }

        if ($this->hasWinzouStateMachineGraph('sylius_payment', $stateMachineDefaultAdapter, $stateMachineAdapterMapping)) {
            $container->prependExtensionConfig('winzou_state_machine', [
                'sylius_payment' => [
                    'callbacks' => [
                        'before' => [
                            'tpay_refund_payment' => [
                                'on' => ['refund'],
                                'do' => ['@commerce_weavers_sylius_tpay.refunding.dispatcher.refund', 'dispatch'],
                                'args' => ['object'],
                            ],
                        ],
                    ],
                ],
            ]);
        }

        if ($this->hasWinzouStateMachineGraph('sylius_refund_refund_payment', $stateMachineDefaultAdapter, $stateMachineAdapterMapping)) {
            $container->prependExtensionConfig('winzou_state_machine', [
                'sylius_refund_refund_payment' => [
                    'callbacks' => [
                        'before' => [
                            'tpay_refund_payment' => [
                                'on' => ['complete'],
                                'do' => ['@commerce_weavers_sylius_tpay.refunding.dispatcher.refund', 'dispatch'],
                                'args' => ['object'],
                            ],
                        ],
                    ],
                ],
            ]);
        }
    }

    /**
     * @param array<string, string> $stateMachineAdapterMapping
     */
    private function hasWinzouStateMachineGraph(
        string $graphName,
        string $stateMachineDefaultAdapter,
        array $stateMachineAdapterMapping,
    ): bool {
        return (
            isset($stateMachineAdapterMapping[$graphName])
            && $stateMachineAdapterMapping[$graphName] === 'winzou_state_machine'
        )
        || (
            !isset($stateMachineAdapterMapping[$graphName])
            && $stateMachineDefaultAdapter === 'winzou_state_machine'
        );
    }
}

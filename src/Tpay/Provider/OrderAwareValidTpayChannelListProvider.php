<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class OrderAwareValidTpayChannelListProvider implements OrderAwareValidTpayChannelListProviderInterface
{
    private const FLOAT_AMOUNT_VALUE_TO_INT_MULTIPLIER = 100;

    public function __construct(private readonly ValidTpayChannelListProviderInterface $validTpayChannelListProvider)
    {
    }

    public function provide(OrderInterface $order): array
    {
        $orderTotal = $order->getTotal();
        $channelList = $this->validTpayChannelListProvider->provide();

        foreach ($channelList as $key => $channel) {
            foreach ($channel['constraints'] ?? [] as $constraint) {
                if ('amount' !== $constraint['field']) {
                    continue;
                }

                $constraintValue = (int) $constraint['value'] * self::FLOAT_AMOUNT_VALUE_TO_INT_MULTIPLIER;

                if (
                    ('min' === $constraint['type'] && $orderTotal < $constraintValue) ||
                    ('max' === $constraint['type'] && $orderTotal > $constraintValue)
                ) {
                    unset($channelList[$key]);

                    break;
                }
            }
        }

        return $channelList;
    }
}

<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;

final class AvailableTpayChannelListProvider implements AvailableTpayChannelListProviderInterface
{
    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $channelResolver,
    ) {
    }

    public function provide(): array
    {
        $result = $this->channelResolver->resolve();

        /** @phpstan-var array{
         * id: string,
         * name: string,
         * fullName: string,
         * image: object,
         * available: bool,
         * onlinePayment: bool,
         * instantRedirection: bool,
         * groups: array,
         * constraints: array
         * }
         */
        return array_filter($result, static function (array $channel) {
            return ($channel['available'] ?? false) === true;
        });
    }
}

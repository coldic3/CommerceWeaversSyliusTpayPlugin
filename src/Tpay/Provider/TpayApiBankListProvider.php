<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver\TpayTransactionChannelResolverInterface;

final class TpayApiBankListProvider implements TpayApiBankListProviderInterface
{
    public function __construct(
        private readonly TpayTransactionChannelResolverInterface $channelResolver,
    ) {
    }

    public function provide(): array
    {
        $result = $this->channelResolver->resolve();

        return array_filter($result, static function (array $channel) {
            return
                ($channel['instantRedirection'] ?? false) === true &&
                ($channel['onlinePayment'] ?? false) === true &&
                ($channel['available'] ?? false) === true
            ;
        });
    }
}

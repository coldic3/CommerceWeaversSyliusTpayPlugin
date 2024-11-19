<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver;

use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Factory\GetTpayTransactionsChannelsFactoryInterface;
use CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\Payum\Request\GetTpayTransactionsChannels;
use Payum\Core\Model\ArrayObject;
use Payum\Core\Payum;
use Psr\Log\LoggerInterface;
use Tpay\OpenApi\Utilities\TpayException;

final class TpayTransactionChannelResolver implements TpayTransactionChannelResolverInterface
{
    public function __construct(
        private readonly Payum $payum,
        private readonly ?GetTpayTransactionsChannelsFactoryInterface $getTpayTransactionsChannelsFactory = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
        if (null === $this->getTpayTransactionsChannelsFactory) {
            trigger_deprecation(
                'commerce-weavers/sylius-tpay-plugin',
                '1.0',
                'Not passing a $getTpayTransactionsChannelsFactory to %s constructor is deprecated and will be removed in SyliusTpayPlugin 2.0.',
                self::class,
            );
        }
    }

    public function resolve(): array
    {
        $gateway = $this->payum->getGateway('tpay');

        $value = $this->getTpayTransactionsChannelsFactory?->createNewEmpty()
            ?? new GetTpayTransactionsChannels(new ArrayObject());

        try {
            $gateway->execute($value, true);
        } catch (TpayException $e) {
            $this->logger?->critical('Unable to get banks list. TpayException thrown.', ['exceptionMessage' => $e->getMessage()]);

            return [];
        }

        $result = $value->getResult();

        if (!isset($result['result']) || 'success' !== $result['result']) {
            $this->logger?->critical('Unable to get banks list. The result is not success.', ['responseBody' => json_encode($result)]);

            return [];
        }

        if (!isset($result['channels'])) {
            $this->logger?->critical('Unable to get banks list. The channels key is missing.', ['responseBody' => json_encode($result)]);

            return [];
        }

        $indexedResult = [];
        foreach ($result['channels'] as $tpayTransactionChannel) {
            $indexedResult[$tpayTransactionChannel['id']] = $tpayTransactionChannel;
        }

        return $indexedResult;
    }
}

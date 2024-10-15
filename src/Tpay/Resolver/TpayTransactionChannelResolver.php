<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Resolver;

use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\UnableToGetBankListException;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\GetTpayTransactionsChannels;
use Payum\Core\Model\ArrayObject;
use Payum\Core\Payum;

final class TpayTransactionChannelResolver implements TpayTransactionChannelResolverInterface
{
    public function __construct(
        private readonly Payum $payum,
    ) {
    }

    public function resolve(): array
    {
        $gateway = $this->payum->getGateway('tpay');

        $gateway->execute($value = new GetTpayTransactionsChannels(new ArrayObject()), true);

        $result = $value->getResult();

        if (!isset($result['result']) || 'success' !== $result['result']) {
            throw new UnableToGetBankListException('Unable to get banks list. Response: ' . json_encode($result));
        }

        $indexedResult = [];
        foreach ($result['channels'] as $tpayTransactionChannel) {
            $indexedResult[$tpayTransactionChannel['id']] = $tpayTransactionChannel;
        }

        return $indexedResult;
    }
}

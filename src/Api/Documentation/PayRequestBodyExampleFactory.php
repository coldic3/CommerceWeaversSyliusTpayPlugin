<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Documentation;

final class PayRequestBodyExampleFactory
{
    private const EXAMPLE_VALUE = 'string';

    public static function create(): array
    {
        $commonExampleData = [
            'successUrl' => self::EXAMPLE_VALUE,
            'failureUrl' => self::EXAMPLE_VALUE,
        ];

        return [
            'Pay by link' => [
                'value' => $commonExampleData,
            ],
            'Pay by link (specific channel)' => [
                'value' => $commonExampleData + [
                    'tpayChannelId' => self::EXAMPLE_VALUE,
                ],
            ],
            'Card (new card)' => [
                'value' => $commonExampleData + [
                    'encodedCardData' => self::EXAMPLE_VALUE,
                ],
            ],
            'Card (save new card)' => [
                'value' => $commonExampleData + [
                    'encodedCardData' => self::EXAMPLE_VALUE,
                    'saveCard' => true,
                ],
            ],
            'Card (saved card)' => [
                'value' => $commonExampleData + [
                    'savedCardId' => self::EXAMPLE_VALUE,
                ],
            ],
            'Blik (token)' => [
                'value' => $commonExampleData + [
                    'blikToken' => self::EXAMPLE_VALUE,
                ],
            ],
            'Blik (save alias)' => [
                'value' => $commonExampleData + [
                    'blikToken' => self::EXAMPLE_VALUE,
                    'blikAliasAction' => 'register',
                ],
            ],
            'Blik (use alias)' => [
                'value' => $commonExampleData + [
                    'blikAliasAction' => 'apply',
                ],
            ],
            'Google Pay' => [
                'value' => $commonExampleData + [
                    'googlePayToken' => self::EXAMPLE_VALUE,
                ],
            ],
            'Apple Pay' => [
                'value' => $commonExampleData + [
                    'applePayToken' => self::EXAMPLE_VALUE,
                ],
            ],
            'Visa Mobile' => [
                'value' => $commonExampleData + [
                    'visaMobilePhoneNumber' => self::EXAMPLE_VALUE,
                ],
            ],
        ];
    }
}

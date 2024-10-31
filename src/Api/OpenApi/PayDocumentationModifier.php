<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\OpenApi;

use ApiPlatform\Core\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\DocumentationModifierInterface;

final class PayDocumentationModifier implements DocumentationModifierInterface
{
    private const EXAMPLE_VALUE = 'string';

    public function __construct(private readonly string $apiShopRoutePrefix)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $paths = $docs->getPaths();
        $path = sprintf('%s/orders/{tokenValue}/pay', $this->apiShopRoutePrefix);
        $pathItem = $paths->getPath($path);

        if (null === $pathItem) {
            return $docs;
        }

        $post = $pathItem->getPost();

        if (null === $post) {
            return $docs;
        }

        $requestBody = $post->getRequestBody();

        if (null === $requestBody) {
            return $docs;
        }

        $content = $requestBody->getContent();

        /** @var MediaType $mediaType */
        $mediaType = $content['application/ld+json'];

        $commonExampleData = [
            'successUrl' => self::EXAMPLE_VALUE,
            'failureUrl' => self::EXAMPLE_VALUE,
        ];

        $content['application/ld+json'] = $mediaType->withExamples(new \ArrayObject([
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
        ]));

        $pathItem = $pathItem->withPost(
            $post->withRequestBody(
                $requestBody->withContent($content),
            ),
        );

        $paths->addPath($path, $pathItem);

        return $docs->withPaths($paths);
    }
}

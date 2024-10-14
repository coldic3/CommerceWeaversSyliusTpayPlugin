<?php

declare(strict_types=1);

namespace Api\Shop;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\JsonApiTestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils\OrderPlacerTrait;

final class CancellingPaymentTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    public function test_it_allows_to_cancel_payment(): void
    {
        $this->loadFixturesFromDirectory('shop/cancelling_payment');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_blik');

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/api/v2/shop/orders/%s/cancel-last-payment', $order->getTokenValue()),
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([]),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    public function test_it_prevents_from_cancelling_payment_which_cannot_be_cancelled(): void
    {
        $this->loadFixturesFromDirectory('shop/cancelling_payment');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_blik');
        $this->payOrder($order);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/api/v2/shop/orders/%s/cancel-last-payment', $order->getTokenValue()),
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([]),
        );

        $response = $this->client->getResponse();
        /** @var string $responseContent */
        $responseContent = $response->getContent();

        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('cannot be cancelled.', $responseContent);
    }
}

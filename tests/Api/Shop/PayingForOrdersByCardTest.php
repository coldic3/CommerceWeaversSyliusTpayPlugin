<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api\Shop;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\DataFixtures\EncodedCardData;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\JsonApiTestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils\OrderPlacerTrait;

final class PayingForOrdersByCardTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    public function test_paying_with_a_valid_blik_token_for_an_order(): void
    {
        $this->loadFixturesFromDirectory('shop/paying_for_orders_by_card');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_card');

        $this->client->request(
            Request::METHOD_POST,
            sprintf('/api/v2/shop/orders/%s/pay', $order->getTokenValue()),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'encodedCardData' => EncodedCardData::VALID_CARD,
            ]),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertResponse($response, 'shop/paying_for_orders_by_card/test_paying_with_a_valid_card_for_an_order');
    }

    private function doPlaceOrder(
        string $tokenValue,
        string $email = 'sylius@example.com',
        string $productVariantCode = 'MUG_BLUE',
        string $shippingMethodCode = 'UPS',
        string $paymentMethodCode = 'tpay',
        int $quantity = 1,
        ?\DateTimeImmutable $checkoutCompletedAt = null,

    ): OrderInterface {
        $this->checkSetUpOrderPlacerCalled();

        $this->pickUpCart($tokenValue);
        $this->addItemToCart($productVariantCode, $quantity, $tokenValue);
        $cart = $this->updateCartWithAddressAndCouponCode($tokenValue, $email);
        $this->dispatchShippingMethodChooseCommand(
            $tokenValue,
            $shippingMethodCode,
            (string)$cart->getShipments()->first()->getId(),
        );
        $this->dispatchPaymentMethodChooseCommand(
            $tokenValue,
            $paymentMethodCode,
            (string)$cart->getLastPayment()->getId(),
        );

        $order = $this->dispatchCompleteOrderCommand($tokenValue);

        $this->setCheckoutCompletedAt($order, $checkoutCompletedAt);

        return $order;
    }
}

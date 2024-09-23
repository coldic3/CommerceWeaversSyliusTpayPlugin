<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Api\Shop;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\JsonApiTestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils\CardEncrypterTrait;
use Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils\OrderPlacerTrait;

final class PayingForOrdersByCardTest extends JsonApiTestCase
{
    use CardEncrypterTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    public function test_paying_with_a_valid_encrypted_card_data_for_an_order(): void
    {
        $this->loadFixturesFromDirectory('shop/paying_for_orders_by_card');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_card');

        $this->client->request(
            Request::METHOD_POST,
            sprintf('/api/v2/shop/orders/%s/pay', $order->getTokenValue()),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'encodedCardData' => $this->encryptCardData(
                    '2223 0002 8000 0016',
                    new \DateTimeImmutable('2029-12-31'),
                    '123',
                ),
            ]),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
        $this->assertResponse($response, 'shop/paying_for_orders_by_card/test_paying_with_a_valid_card_for_an_order');
    }

    /**
     * @dataProvider data_provider_paying_without_a_card_data_when_a_tpay_card_payment_has_been_chosen
     */
    public function test_paying_without_a_card_data_when_a_tpay_card_payment_has_been_chosen(array $content): void
    {
        $this->loadFixturesFromDirectory('shop/paying_for_orders_by_card');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_card');

        $this->client->request(
            Request::METHOD_POST,
            sprintf('/api/v2/shop/orders/%s/pay', $order->getTokenValue()),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode($content),
        );

        $response = $this->client->getResponse();

        $this->assertResponseViolations($response, [
            [
                'propertyPath' => 'encodedCardData',
                'message' => 'The card data is required.',
            ]
        ]);
    }

    public static function data_provider_paying_without_a_card_data_when_a_tpay_card_payment_has_been_chosen(): iterable
    {
        yield 'empty content' => [[]];
        yield 'content with a BLIK token' => [['blikToken' => '777123']];
    }

    public function test_paying_with_providing_an_empt_card_data(): void
    {
        $this->loadFixturesFromDirectory('shop/paying_for_orders_by_card');

        $order = $this->doPlaceOrder('t0k3n', paymentMethodCode: 'tpay_card');

        $this->client->request(
            Request::METHOD_POST,
            sprintf('/api/v2/shop/orders/%s/pay', $order->getTokenValue()),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode(['encodedCardData' => '']),
        );

        $response = $this->client->getResponse();

        $this->assertResponseViolations($response, [
            [
                'propertyPath' => 'encodedCardData',
                'message' => 'The card data cannot be empty.',
            ]
        ]);    }

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

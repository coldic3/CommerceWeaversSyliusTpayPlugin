<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\CardPayment\Form\Type;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Form\Type\TpayCreditCardChoiceType;
use CommerceWeavers\SyliusTpayPlugin\CardPayment\Repository\CreditCardRepositoryInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TpayCreditCardChoiceTypeTest extends TypeTestCase
{
    use ProphecyTrait;

    private TokenStorageInterface|ObjectProphecy $tokenStorage;
    private TranslatorInterface|ObjectProphecy $translator;
    private CreditCardRepositoryInterface|ObjectProphecy $creditCardRepository;
    private CartContextInterface|ObjectProphecy $cartContext;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $this->translator = $this->prophesize(TranslatorInterface::class);
        $this->creditCardRepository = $this->prophesize(CreditCardRepositoryInterface::class);
        $this->cartContext = $this->prophesize(CartContextInterface::class);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $type = new TpayCreditCardChoiceType(
            $this->tokenStorage->reveal(),
            $this->translator->reveal(),
            $this->creditCardRepository->reveal(),
            $this->cartContext->reveal()
        );

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function test_it_configures_options_that_map_customer_credit_cards_to_description_id_map(): void
    {
        $token = $this->prophesize(TokenInterface::class);
        $this->tokenStorage->getToken()->willReturn($token->reveal());

        /** @var ShopUserInterface $user */
        $user = $this->prophesize(ShopUserInterface::class);
        $token->getUser()->willReturn($user->reveal());

        /** @var OrderInterface $order */
        $order = $this->prophesize(OrderInterface::class);
        $this->cartContext->getCart()->willReturn($order->reveal());

        /** @var ChannelInterface $channel */
        $channel = $this->prophesize(ChannelInterface::class);
        $order->getChannel()->willReturn($channel->reveal());

        /** @var CustomerInterface $customer */
        $customer = $this->prophesize(CustomerInterface::class);
        $user->getCustomer()->willReturn($customer->reveal());

        /** @var CreditCardInterface $creditCard */
        $creditCard = $this->prophesize(CreditCardInterface::class);
        $creditCard->getId()->willReturn('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $creditCard->getTail()->willReturn('1234');
        $creditCard->getBrand()->willReturn('visa');
        $creditCard->getExpirationDate()->willReturn(new \DateTimeImmutable('2022-12-01'));

        $this->translator->trans(
            'commerce_weavers_sylius_tpay.shop.credit_card.card_selection_one_liner',
            [
                '%brand%' => 'visa',
                '%tail%' => '1234',
                '%expires%' => '12-2022',
            ],
            'messages'
        )->willReturn('visa ending in 1234 expiring 12-2022');

        $this->creditCardRepository->findByCustomerAndChannel($customer->reveal(), $channel->reveal())->willReturn([$creditCard->reveal()]);

        $resolver = new OptionsResolver();

        $type = new TpayCreditCardChoiceType(
            $this->tokenStorage->reveal(),
            $this->translator->reveal(),
            $this->creditCardRepository->reveal(),
            $this->cartContext->reveal()
        );
        $type->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertArrayHasKey('choices', $options);
        $this->assertSame(['visa ending in 1234 expiring 12-2022' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479'], $options['choices']);
    }

    public function test_it_returns_empty_choices_when_no_credit_cards_are_found()
    {
        $token = $this->prophesize(TokenInterface::class);
        $this->tokenStorage->getToken()->willReturn($token->reveal());

        /** @var ShopUserInterface $user */
        $user = $this->prophesize(ShopUserInterface::class);
        $token->getUser()->willReturn($user->reveal());

        /** @var OrderInterface $order */
        $order = $this->prophesize(OrderInterface::class);
        $this->cartContext->getCart()->willReturn($order->reveal());

        /** @var ChannelInterface $channel */
        $channel = $this->prophesize(ChannelInterface::class);
        $order->getChannel()->willReturn($channel->reveal());

        /** @var CustomerInterface $customer */
        $customer = $this->prophesize(CustomerInterface::class);
        $user->getCustomer()->willReturn($customer->reveal());

        $this->creditCardRepository->findByCustomerAndChannel($customer->reveal(), $channel->reveal())->willReturn([]);

        $resolver = new OptionsResolver();
        $type = new TpayCreditCardChoiceType(
            $this->tokenStorage->reveal(),
            $this->translator->reveal(),
            $this->creditCardRepository->reveal(),
            $this->cartContext->reveal()
        );
        $type->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertArrayHasKey('choices', $options);
        $this->assertEmpty($options['choices']);
    }
}

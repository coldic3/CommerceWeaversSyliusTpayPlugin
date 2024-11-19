<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Form\Type;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Repository\CreditCardRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TpayCreditCardChoiceType extends AbstractType
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly TranslatorInterface $translator,
        private readonly CreditCardRepositoryInterface $creditCardRepository,
        private readonly CartContextInterface $cartContext,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $token = $this->tokenStorage->getToken();

        $user = $token?->getUser();

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        $channel = $order->getChannel();
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $creditCards = $this->creditCardRepository->findByCustomerAndChannel($customer, $channel);

        $choices = [];

        if (count($creditCards) !== 0) {
            $choices = $this->mapChoices($creditCards);
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'required' => false,
            'label' => 'commerce_weavers_sylius_tpay.shop.order_summary.card.use_saved_credit_card.label',
            'placeholder' => new TranslatableMessage('commerce_weavers_sylius_tpay.shop.credit_card.use_new_card'),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'cw_tpay_credit_card_choice';
    }

    private function mapChoices(array $creditCards): array
    {
        $choices = [];

        foreach ($creditCards as $creditCard) {
            $stringifiedCard = $this->translator->trans(
                'commerce_weavers_sylius_tpay.shop.credit_card.card_selection_one_liner',
                [
                    '%brand%' => $creditCard->getBrand(),
                    '%tail%' => $creditCard->getTail(),
                    '%expires%' => $creditCard->getExpirationDate()->format('m-Y'),
                ],
                'messages',
            );

            $choices[$stringifiedCard] = $creditCard->getId();
        }

        return $choices;
    }
}

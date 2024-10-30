<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Form\EventListener;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\Repository\CreditCardRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSavedCreditCardsListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly TranslatorInterface $translator,
        private readonly CreditCardRepositoryInterface $creditCardRepository,
    ) {
    }

    public function __invoke(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var OrderInterface|PaymentInterface|mixed $data */
        $data = $form->getParent()->getData();

        if ($data instanceof PaymentInterface) {
            $data = $data->getOrder();
        }

        if (!$data instanceof OrderInterface) {
            return;
        }

        $channel = $data->getChannel();

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        $customer = $user?->getCustomer();

        if (!$this->creditCardRepository->hasCustomerAnyCreditCardInGivenChannel($customer, $channel)) {
            return;
        }

        $creditCards = $this->creditCardRepository->findByCustomerAndChannel($customer, $channel);

        $choices = [];

        foreach ($creditCards as $creditCard) {
            $stringifiedCard = $this->translator->trans(
                'commerce_weavers_sylius_tpay.shop.credit_card.card_selection_one_liner',
                [
                    '%brand%' => $creditCard->getBrand(),
                    '%tail%' => $creditCard->getTail(),
                    '%expires%' => $creditCard->getExpirationDate()->format('m-Y'),
                ], 'messages'
            );

            $choices[$stringifiedCard] = $creditCard->getId();
        }

        VarDumper::dump($choices);

        $form
            ->add('useSavedCreditCard', ChoiceType::class,
                [
                    'label' => 'commerce_weavers_sylius_tpay.shop.order_summary.card.use_saved_credit_card.label',
                    'placeholder' => new TranslatableMessage('commerce_weavers_sylius_tpay.shop.credit_card.use_new_card'),
                    'required' => false,
                    'choices' => $choices,
                ]
            )
        ;
    }
}

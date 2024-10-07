<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Form\Extension;

use Setono\SyliusGiftCardPlugin\Model\OrderItemInterface;
use Setono\SyliusGiftCardPlugin\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly CurrencyContextInterface $currencyContext,
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            AddToCartType::class,
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addFormFields']);

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'populateCartItem']);
    }

    public function addFormFields(FormEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof AddToCartCommandInterface) {
            return;
        }

        $cartItem = $data->getCartItem();
        if (!$cartItem instanceof OrderItemInterface) {
            return;
        }

        $variant = $cartItem->getVariant();
        if (!$variant instanceof ProductVariantInterface) {
            return;
        }

        $product = $variant->getProduct();
        if (!$product instanceof ProductInterface || !$product->isGiftCard()) {
            return;
        }

        $event
            ->getForm()
            ->add('amount', MoneyType::class, [
                'label' => 'setono_sylius_gift_card.form.add_to_cart.gift_card_information.amount',
                'currency' => $this->currencyContext->getCurrencyCode(),
                'data' => $this->getDefaultAmount($variant),
                'mapped' => false,
            ])
            ->add('customMessage', TextareaType::class, [
                'required' => false,
                'label' => 'setono_sylius_gift_card.form.add_to_cart.gift_card_information.custom_message',
                'attr' => [
                    'placeholder' => 'setono_sylius_gift_card.form.add_to_cart.gift_card_information.custom_message_placeholder',
                ],
                'mapped' => false,
            ])
        ;
    }

    public function populateCartItem(FormEvent $event): void
    {
        $data = $event->getData();
        if (!$data instanceof AddToCartCommandInterface) {
            return;
        }

        $cartItem = $data->getCartItem();
        if (!$cartItem instanceof OrderItemInterface) {
            return;
        }

        $product = $cartItem->getProduct();
        if (!$product instanceof ProductInterface || !$product->isGiftCard()) {
            return;
        }

        $amount = $event->getForm()->get('amount')->getData();
        $customMessage = $event->getForm()->get('customMessage')->getData();

        if (!is_int($amount)) {
            return;
        }

        $cartItem->setUnitPrice($amount);
        $cartItem->setImmutable(true);
        $cartItem->setDetails([
            'giftCardMessage' => $customMessage,
        ]);
    }

    private function getDefaultAmount(ProductVariantInterface $variant): int
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $channelPricing = $variant->getChannelPricingForChannel($channel);

        return $channelPricing?->getPrice() ?? 100;
    }
}

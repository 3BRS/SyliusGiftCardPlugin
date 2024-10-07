<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Factory;

use DateTimeImmutable;
use DateTimeInterface;
use Setono\SyliusGiftCardPlugin\Generator\GiftCardCodeGeneratorInterface;
use Setono\SyliusGiftCardPlugin\Model\GiftCardInterface;
use Setono\SyliusGiftCardPlugin\Model\OrderItemInterface;
use Setono\SyliusGiftCardPlugin\Model\ProductInterface;
use Setono\SyliusGiftCardPlugin\Provider\GiftCardConfigurationProviderInterface;
use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class GiftCardFactory implements GiftCardFactoryInterface
{
    public function __construct(
        private readonly FactoryInterface $decoratedFactory,
        private readonly GiftCardCodeGeneratorInterface $giftCardCodeGenerator,
        private readonly GiftCardConfigurationProviderInterface $giftCardConfigurationProvider,
        /** @psalm-suppress DeprecatedInterface */
        private readonly DateTimeProvider $dateTimeProvider,
        private readonly CurrencyContextInterface $currencyContext,
    ) {
    }

    public function createNew(): GiftCardInterface
    {
        /** @var GiftCardInterface|object $giftCard */
        $giftCard = $this->decoratedFactory->createNew();
        Assert::isInstanceOf($giftCard, GiftCardInterface::class);

        $giftCard->setCode($this->giftCardCodeGenerator->generate());

        return $giftCard;
    }

    public function createForChannel(ChannelInterface $channel): GiftCardInterface
    {
        $giftCard = $this->createNew();
        $giftCard->setChannel($channel);

        $channelConfiguration = $this->giftCardConfigurationProvider->getConfigurationForGiftCard($giftCard);
        $validityPeriod = $channelConfiguration->getDefaultValidityPeriod();
        if (null !== $validityPeriod) {
            $today = $this->dateTimeProvider->today();
            // Since the interface is types to DateTimeInterface, the modify method does not exist
            // whereas it does in DateTime and DateTimeImmutable
            Assert::isInstanceOf($today, DateTimeImmutable::class);
            /** @var DateTimeInterface $today */
            $today = $today->modify('+' . $validityPeriod);
            $giftCard->setExpiresAt($today);
        }

        return $giftCard;
    }

    public function createForChannelFromAdmin(ChannelInterface $channel): GiftCardInterface
    {
        $giftCard = $this->createForChannel($channel);
        $giftCard->setOrigin(GiftCardInterface::ORIGIN_ADMIN);

        return $giftCard;
    }

    public function createFromOrder(OrderInterface $order): array
    {
        $giftCards = [];

        foreach ($order->getItems() as $orderItem) {
            $product = $orderItem->getProduct();
            if (!$product instanceof ProductInterface || !$product->isGiftCard()) {
                continue;
            }

            $giftCards[] = $this->createFromOrderItem($orderItem);
        }

        return array_merge(...$giftCards);
    }

    public function createFromOrderItem(OrderItemInterface $orderItem): array
    {
        /** @var OrderInterface $order */
        $order = $orderItem->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);

        $giftCards = [];

        for ($i = 0; $i < $orderItem->getQuantity(); ++$i) {
            $giftCard = $this->createForChannel($channel);
            $giftCard->setCustomer($customer);
            $giftCard->setOrigin(GiftCardInterface::ORIGIN_ORDER);
            $giftCard->setCurrencyCode($currencyCode);
            $giftCard->setAmount($orderItem->getUnitPrice());

            $giftCards[] = $giftCard;
        }

        return $giftCards;
    }

    public function createFromOrderItemUnitAndCart(
        OrderItemUnitInterface $orderItemUnit,
        OrderInterface $cart,
    ): GiftCardInterface {
        $channel = $cart->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);
        $currencyCode = $cart->getCurrencyCode();
        Assert::notNull($currencyCode);

        $giftCard = $this->createForChannel($channel);
        //$giftCard->setOrderItemUnit($orderItemUnit); todo
        $giftCard->setAmount($orderItemUnit->getTotal());
        $giftCard->setCurrencyCode($currencyCode);
        $giftCard->setChannel($channel);
        $giftCard->disable();
        $giftCard->setOrigin(GiftCardInterface::ORIGIN_ORDER);

        return $giftCard;
    }

    public function createExample(): GiftCardInterface
    {
        $giftCard = $this->createNew();
        $giftCard->setAmount(1500);
        $giftCard->setCurrencyCode($this->currencyContext->getCurrencyCode());
        $giftCard->setExpiresAt(new DateTimeImmutable('+3 years'));
        $giftCard->setCustomMessage('Hi there, beautiful! Thought I wanted to make your day even better with this gift card');

        return $giftCard;
    }
}

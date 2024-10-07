<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Factory;

use Setono\SyliusGiftCardPlugin\Model\GiftCardInterface;
use Setono\SyliusGiftCardPlugin\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface GiftCardFactoryInterface extends FactoryInterface
{
    public function createNew(): GiftCardInterface;

    public function createForChannel(ChannelInterface $channel): GiftCardInterface;

    public function createForChannelFromAdmin(ChannelInterface $channel): GiftCardInterface;

    /**
     * This will return a list of gift cards based on all the order items that are gift cards
     *
     * @return list<GiftCardInterface>
     */
    public function createFromOrder(OrderInterface $order): array;

    /**
     * This will return a list of gift cards. The length of the list equals the quantity on the order item
     *
     * @return list<GiftCardInterface>
     */
    public function createFromOrderItem(OrderItemInterface $orderItem): array;

    public function createFromOrderItemUnitAndCart(
        OrderItemUnitInterface $orderItemUnit,
        OrderInterface $cart,
    ): GiftCardInterface;

    /**
     * Create an example GiftCard that is used to generate the example PDF for configuration live rendering
     */
    public function createExample(): GiftCardInterface;
}

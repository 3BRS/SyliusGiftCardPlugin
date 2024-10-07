<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Tests\Application\Model;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusGiftCardPlugin\Model\OrderItemInterface as SetonoSyliusGiftCardOrderItemInterface;
use Setono\SyliusGiftCardPlugin\Model\OrderItemTrait as SetonoSyliusGiftCardOrderItemTrait;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_order_item")
 */
class OrderItem extends BaseOrderItem implements SetonoSyliusGiftCardOrderItemInterface
{
    use SetonoSyliusGiftCardOrderItemTrait;
}

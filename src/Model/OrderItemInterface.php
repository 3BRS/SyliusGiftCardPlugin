<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface
{
    /**
     * @return Collection<array-key, GiftCardInterface>
     */
    public function getBoughtGiftCards(): Collection;

    public function getDetails(): array;

    /**
     * @param array<string, mixed> $details
     */
    public function setDetails(array $details): void;
}

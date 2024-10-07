<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;

trait OrderItemTrait
{
    /**
     * @ORM\OneToMany(targetEntity="Setono\SyliusGiftCardPlugin\Model\GiftCardInterface", mappedBy="orderItem")
     *
     * @var Collection<array-key, GiftCardInterface>
     */
    protected Collection $boughtGiftCards;

    /**
     * @ORM\Column(type="array", nullable=true)
     *
     * @var array<string, mixed>|null
     */
    protected ?array $details = null;

    public function getBoughtGiftCards(): Collection
    {
        return $this->boughtGiftCards;
    }

    public function getDetails(): array
    {
        return $this->details ?? [];
    }

    /**
     * @param array<string, mixed> $details
     */
    public function setDetails(array $details): void
    {
        $this->details = [] === $details ? null : $details;
    }

    public function equals(BaseOrderItemInterface $item): bool
    {
        // todo
        return parent::equals($item);

        return parent::equals($item) && !$this->getProduct()->isGiftCard();
    }
}

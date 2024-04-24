<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Tests\Behat\Page\Shop\Cart;

use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface as BaseSummaryPageInterface;

interface SummaryPageInterface extends BaseSummaryPageInterface
{
    public function applyGiftCard(string $giftCardCode): void;

    public function getGiftCardTotal(): string;
}

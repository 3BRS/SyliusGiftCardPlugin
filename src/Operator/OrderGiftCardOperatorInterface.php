<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Operator;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderGiftCardOperatorInterface
{
    public function enable(OrderInterface $order): void;

    public function disable(OrderInterface $order): void;
}

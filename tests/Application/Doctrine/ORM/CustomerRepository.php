<?php

declare(strict_types=1);

namespace Setono\SyliusGiftCardPlugin\Tests\Application\Doctrine\ORM;

use Setono\SyliusGiftCardPlugin\Doctrine\ORM\CustomerRepositoryTrait as SetonoSyliusGiftCardPluginCustomerRepositoryTrait;
use Setono\SyliusGiftCardPlugin\Repository\CustomerRepositoryInterface as SetonoSyliusGiftCardPluginCustomerRepositoryInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository implements SetonoSyliusGiftCardPluginCustomerRepositoryInterface
{
    use SetonoSyliusGiftCardPluginCustomerRepositoryTrait;
}

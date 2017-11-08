<?php
declare(strict_types=1);

namespace DomainShop\Persistence;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use DomainShop\Entity\OrderRepository;

final class FileBasedOrderRepository implements OrderRepository
{
    public function save(Order $order): void
    {
        Database::persist($order);
    }

    public function getById(string $id): Order
    {
        return Database::retrieve(Order::class, $id);
    }
}

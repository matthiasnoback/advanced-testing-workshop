<?php
declare(strict_types=1);

namespace DomainShop\Persistence;

use DomainShop\Entity\Order;
use DomainShop\Entity\OrderRepository;

final class InMemoryOrderRepository implements OrderRepository
{
    private $orders = [];

    public function save(Order $order): void
    {
        $this->orders[(string)$order->id()] = $order;
    }

    public function getById(string $id): Order
    {
        return $this->orders[$id];
    }
}

<?php
declare(strict_types=1);

namespace DomainShop\Entity;

interface OrderRepository
{
    public function save(Order $order): void;

    public function getById(string $id): Order;
}

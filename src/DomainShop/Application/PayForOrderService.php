<?php
declare(strict_types=1);

namespace DomainShop\Application;

use Common\Persistence\Database;
use DomainShop\Entity\Order;

class PayForOrderService
{
    public function __invoke(string $orderId)
    {
        $order = Database::retrieve(Order::class, $orderId);
        $order->setWasPaid(true);
        Database::persist($order);
    }
}

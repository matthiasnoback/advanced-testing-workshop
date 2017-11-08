<?php
declare(strict_types=1);

namespace DomainShop\Service;

use Common\Persistence\Database;
use DomainShop\Entity\Order;

final class PayForOrder
{
    public function handle(string $orderId)
    {
        /** @var Order $order */
        $order = Database::retrieve(Order::class, (string)$orderId);

        $order->setWasPaid(true);

        Database::persist($order);
    }
}

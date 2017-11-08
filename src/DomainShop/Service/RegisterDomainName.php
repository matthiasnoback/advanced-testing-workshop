<?php
declare(strict_types=1);

namespace DomainShop\Service;

use Common\Persistence\Database;
use DomainShop\Entity\Order;

final class RegisterDomainName
{
    public function handle($domainName, $name, $emailAddress, $currency): Order
    {
        $orderId = count(Database::retrieveAll(Order::class)) + 1;

        $order = new Order();
        $order->setId($orderId);
        $order->setDomainName($domainName);
        $order->setOwnerName($name);
        $order->setOwnerEmailAddress($emailAddress);
        $order->setPayInCurrency($currency);

        Database::persist($order);

        return $order;
    }
}

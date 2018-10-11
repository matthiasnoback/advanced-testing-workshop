<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DomainShop\Infrastructure;

use Common\Persistence\Database;
use DomainShop\Domain\OrderRepository;
use DomainShop\Entity\Order;

/**
 * @author Julian Prud'homme <julian.prudhomme@akeneo.com>
 */
class FileSystemOrderRepository implements OrderRepository
{
    public function nextId(): int
    {
        return count(Database::retrieveAll(Order::class)) + 1;
    }

    public function retrieve(int $orderId): Order
    {
        return Database::retrieve(Order::class, (string) $orderId);
    }

    public function persist(Order $order): void
    {
        Database::persist($order);
    }

}

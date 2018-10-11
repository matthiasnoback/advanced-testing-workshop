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

use DomainShop\Domain\OrderRepository;
use DomainShop\Entity\Order;

/**
 * @author Julian Prud'homme <julian.prudhomme@akeneo.com>
 */
class InMemoryOrderRepository implements OrderRepository
{
    private $orders;

    public function __construct()
    {
        $this->orders = [];
    }

    public function nextId(): int
    {
        return count($this->orders) + 1;
    }

    public function retrieve(int $orderId): Order
    {
        if (! array_key_exists($orderId, $this->orders)) {
            throw new \RuntimeException(sprintf('Order %s does not exist', $orderId));
        }

        return $this->orders[$orderId];
    }

    public function persist(Order $order): void
    {
        $this->orders[$order->id()] = $order;
    }
}

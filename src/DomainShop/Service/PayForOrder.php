<?php
declare(strict_types=1);

namespace DomainShop\Service;

use DomainShop\Entity\OrderRepository;

final class PayForOrder
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(string $orderId, string $currency, int $amount): void
    {
        $order = $this->orderRepository->getById($orderId);

        $order->pay($currency, $amount);

        $this->orderRepository->save($order);
    }
}

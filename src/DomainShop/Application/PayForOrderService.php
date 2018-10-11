<?php
declare(strict_types=1);

namespace DomainShop\Application;

use DomainShop\Domain\OrderRepository;

class PayForOrderService
{
    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(string $orderId)
    {
        $order = $this->orderRepository->retrieve((int) $orderId);
        $order->setWasPaid(true);
        $this->orderRepository->persist($order);
    }
}

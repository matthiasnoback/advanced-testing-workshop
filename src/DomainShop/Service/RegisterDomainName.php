<?php
declare(strict_types=1);

namespace DomainShop\Service;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use DomainShop\Entity\OrderRepository;

final class RegisterDomainName
{
    /**
     * @var PricingService
     */
    private $pricingService;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(PricingService $pricingService, OrderRepository $orderRepository)
    {
        $this->pricingService = $pricingService;
        $this->orderRepository = $orderRepository;
    }

    public function handle($domainName, $name, $emailAddress, $payInCurrency): Order
    {
        $orderId = count(Database::retrieveAll(Order::class)) + 1;

        $order = new Order();
        $order->setId($orderId);
        $order->setDomainName($domainName);
        $order->setOwnerName($name);
        $order->setOwnerEmailAddress($emailAddress);
        $order->setPayInCurrency($payInCurrency);

        $order->setPrice($this->pricingService->getPriceForDomainNameExtension(
            $order->getDomainNameExtension(),
            $order->getPayInCurrency()
        ));

        $this->orderRepository->save($order);

        return $order;
    }
}

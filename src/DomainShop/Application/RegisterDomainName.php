<?php

namespace DomainShop\Application;

use Common\Persistence\Database;
use DomainShop\Domain\OrderRepository;
use DomainShop\Entity\Order;
use DomainShop\Entity\Pricing;

class RegisterDomainName
{
    /** @var ExchangeRateProvider */
    private $exchangeRateProvider;
    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(ExchangeRateProvider $exchangeRateProvider, OrderRepository $orderRepository)
    {
        $this->exchangeRateProvider = $exchangeRateProvider;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(string $domainName, string $name, string $emailAddress, string $currency): Order
    {
        $orderId = $this->orderRepository->nextId();
        $order = new Order();
        $order->setId($orderId);
        $order->setDomainName($domainName);
        $order->setOwnerName($name);
        $order->setOwnerEmailAddress($emailAddress);
        $order->setPayInCurrency($currency);

        /** @var Pricing $pricing */
        $pricing = Database::retrieve(Pricing::class, $order->getDomainNameExtension());

        if ($order->getPayInCurrency() !== $pricing->getCurrency()) {
            $rate = $this->exchangeRateProvider->getExchangeRate(
                $pricing->getCurrency(),
                $order->getPayInCurrency()
            );
            $amount = (int) round($pricing->getAmount() * $rate);
        } else {
            $amount = $pricing->getAmount();
        }

        $order->setAmount($amount);

        $this->orderRepository->persist($order);

        return $order;
    }
}

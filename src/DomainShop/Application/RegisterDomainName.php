<?php

namespace DomainShop\Application;

use DomainShop\Domain\OrderRepository;
use DomainShop\Domain\PricingRepository;
use DomainShop\Entity\Order;
use DomainShop\Infrastructure\FileSystemPricingRepository;

class RegisterDomainName
{
    /** @var ExchangeRateProvider */
    private $exchangeRateProvider;
    /** @var OrderRepository */
    private $orderRepository;
    /** @var PricingRepository */
    private $pricingRepository;

    public function __construct(
        ExchangeRateProvider $exchangeRateProvider,
        OrderRepository $orderRepository,
        PricingRepository $pricingRepository
    ) {
        $this->exchangeRateProvider = $exchangeRateProvider;
        $this->orderRepository = $orderRepository;
        $this->pricingRepository = $pricingRepository;
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

        $pricing = $this->pricingRepository->retrieve($order->getDomainNameExtension());

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

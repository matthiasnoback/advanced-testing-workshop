<?php

namespace DomainShop\Application;

use Common\Persistence\Database;
use DomainShop\Entity\Order;
use DomainShop\Entity\Pricing;

class RegisterDomainName
{
    /** @var ExchangeRateProvider */
    private $exchangeRateProvider;

    public function __construct(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    public function __invoke(string $domainName, string $name, string $emailAddress, string $currency): Order
    {
        $orderId = count(Database::retrieveAll(Order::class)) + 1;
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

        Database::persist($order);

        return $order;
    }
}

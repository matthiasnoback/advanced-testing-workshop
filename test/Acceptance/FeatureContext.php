<?php

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Common\Persistence\Database;
use DomainShop\Application\ExchangeRateProvider;
use DomainShop\Application\PayForOrderService;
use DomainShop\Application\RegisterDomainName;
use DomainShop\Application\SetPriceService;
use DomainShop\Domain\OrderRepository;
use DomainShop\Entity\Order;
use DomainShop\Infrastructure\FixedExchangeRate;
use PHPUnit\Framework\Assert;

final class FeatureContext implements Context
{
    private $container;

    private $orderId;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->container = require(__DIR__ . '/../../app/container.php');
    }

    /**
     * @Given I register :domain to :name with email address :email and I want to pay in :currency
     */
    public function iRegisterToWithEmailAddressAndIWantToPayInUsd($domain, $name, $email, $currency)
    {
        $registerDomainName = $this->container->get(RegisterDomainName::class);
        $order = ($registerDomainName)($domain, $name, $email, $currency);
        $this->orderId = $order->id();
    }

    /**
     * @Given I pay :amount USD for it
     */
    public function iPayUsdForIt($amount)
    {
        $payForOrder = $this->container->get(PayForOrderService::class);
        ($payForOrder)($this->orderId);
    }

    /**
     * @Then the order was paid
     */
    public function theOrderWasPaid()
    {
        $order = $this->container->get(OrderRepository::class)->retrieve($this->orderId);

        Assert::assertTrue($order->wasPaid());
    }

    /**
     * @Given a :extension domain name costs :currency :amount
     */
    public function aComDomainNameCostsEur($extension, $currency, $amount)
    {
        $setPriceService = $this->container->get(SetPriceService::class);
        ($setPriceService)($extension, $currency, $amount);
    }

    /**
     * @Given the exchange rate :fromCurrency to :toCurrency is :amount
     */
    public function theExchangeRateEurToUsdIs($fromCurrency, $toCurrency, $amount)
    {
        /** @var FixedExchangeRate $exchangeRateProvider */
        $exchangeRateProvider = $this->container->get(ExchangeRateProvider::class);
        $exchangeRateProvider->setExchangeRate($fromCurrency, $toCurrency, $amount);
    }
}

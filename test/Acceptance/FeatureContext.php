<?php

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Common\Persistence\Database;
use DomainShop\Application\PayForOrderService;
use DomainShop\Application\RegisterDomainName;
use DomainShop\Application\SetPriceService;
use DomainShop\Entity\Order;
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
        $order = Database::retrieve(Order::class, $this->orderId);

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
     * @Given the exchange rate EUR to USD is :arg1
     */
    public function theExchangeRateEurToUsdIs($arg1)
    {
        throw new PendingException();
    }
}

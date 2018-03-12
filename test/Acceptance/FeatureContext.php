<?php

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use DomainShop\Entity\Pricing;
use DomainShop\Persistence\FileBasedOrderRepository;
use DomainShop\Persistence\FileBasedPricingRepository;
use DomainShop\Persistence\InMemoryOrderRepository;
use DomainShop\Persistence\InMemoryPricingRepository;
use DomainShop\Service\ExchangeRate\FakeExchangeRateService;
use DomainShop\Service\PayForOrder;
use DomainShop\Service\PricingService;
use DomainShop\Service\RegisterDomainName;

final class FeatureContext implements Context
{
    /**
     * @var FakeExchangeRateService
     */
    private $exchangeRateService;

    /**
     * @var null|string
     */
    private $orderId;

    /**
     * @var InMemoryPricingRepository
     */
    private $pricingRepository;

    /**
     * @var InMemoryOrderRepository
     */
    private $orderRepository;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->exchangeRateService = new FakeExchangeRateService();
        $this->pricingRepository = new InMemoryPricingRepository();
        $this->orderRepository = new InMemoryOrderRepository();
    }

    private static function floatStringToIntAmount(string $amount): int
    {
        $floatAmount = (float)$amount;

        return (int)($floatAmount * 100);
    }

    /**
     * @Given /^a (\.[a-z]+) domain name costs ([A-Z]+) (\d+\.\d+)$/
     */
    public function aDomainNameCosts(string $domainNameExtension, string $currency, string $amount)
    {
        $pricing = new Pricing();
        $pricing->setExtension($domainNameExtension);
        $pricing->setCurrency($currency);
        $pricing->setAmount(self::floatStringToIntAmount($amount));

        $this->pricingRepository->save($pricing);
    }

    /**
     * @Given /^the exchange rate ([A-Z]+) to ([A-Z]+) is (\d+\.\d+)$/
     */
    public function theExchangeRateIs(string $fromCurrency, string $toCurrency, string $rate)
    {
        $this->exchangeRateService->setExchangeRate($fromCurrency, $toCurrency, (float)$rate);
    }

    /**
     * @Given /^I register "([^"]*)" to "([^"]*)" with email address "([^"]*)" and I want to pay in ([A-Z]+)$/
     */
    public function iRegisterADomainName(string $domainName, string $name, string $emailAddress, string $payInCurrency)
    {
        $service = new RegisterDomainName(
            new PricingService($this->pricingRepository, $this->exchangeRateService),
            $this->orderRepository
        );

        $order = $service->handle($domainName, $name, $emailAddress, $payInCurrency);

        $this->orderId = $order->id();
    }

    /**
     * @Given /^I pay (\d+\.\d+) ([A-Z]+) for it$/
     */
    public function iPayForIt(string $amount, string $currency)
    {
        $service = new PayForOrder($this->orderRepository);

        $service->handle($this->orderId, $currency, self::floatStringToIntAmount($amount));
    }

    /**
     * @Then /^the order was paid$/
     */
    public function theOrderWasPaid()
    {
        $order = $this->orderRepository->getById($this->orderId);

        assertTrue($order->wasPaid());
    }
}

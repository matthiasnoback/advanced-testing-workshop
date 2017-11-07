<?php

namespace Test\System;

use Behat\Mink\Driver\GoutteDriver;
use Behat\MinkExtension\Context\MinkContext;

final class FeatureContext extends MinkContext
{
    /**
     * @Given /^a "([^"]*)" domain name costs "([^"]*)"$/
     *
     * @param string $extension
     * @param string $price
     */
    public function aDomainNameCosts($extension, $price)
    {
        [$currency, $amount] = explode(' ', $price);
        $amount = round((float)$amount * 100);

        /** @var GoutteDriver $driver */
        $driver = $this->getSession()->getDriver();
        $driver->getClient()->request('POST', $this->locatePath('/set-price'), [
            'extension' => $extension,
            'currency' => $currency,
            'amount' => $amount
        ]);
        $this->assertResponseStatus(200);
    }
}

<?php

namespace Test\System;

use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Driver\GoutteDriver;
use Behat\MinkExtension\Context\MinkContext;

final class FeatureContext extends MinkContext
{
    /**
     * @BeforeStep
     */
    public function configureLiveCoverageId(BeforeStepScope $scope)
    {
        $featureFile = $scope->getFeature()->getFile();
        $coverageId = pathinfo($featureFile, PATHINFO_FILENAME);
        $this->getSession()->setCookie('coverage_id', $coverageId);
    }

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

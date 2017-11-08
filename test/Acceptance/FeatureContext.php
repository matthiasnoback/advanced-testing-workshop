<?php

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

final class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I want to check the availability of :arg1
     */
    public function iWantToCheckTheAvailabilityOf($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given it turns out to be available
     */
    public function itTurnsOutToBeAvailable()
    {
        throw new PendingException();
    }

    /**
     * @When I register it
     */
    public function iRegisterIt()
    {
        throw new PendingException();
    }

    /**
     * @When I fill in my name (:arg1) and email address (:arg2)
     */
    public function iFillInMyNameAndEmailAddress($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @When I pay EUR :arg1 for it
     */
    public function iPayEurForIt($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then the domain name is mine
     */
    public function theDomainNameIsMine()
    {
        throw new PendingException();
    }
}

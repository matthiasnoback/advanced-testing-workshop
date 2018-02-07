<?php
declare(strict_types=1);

namespace BehatRemoteCodeCoverage;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Webmozart\Assert\Assert;
use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Mink\Mink;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use LiveCodeCoverage\Storage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RemoteCodeCoverageListener implements EventSubscriberInterface
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var string
     */
    private $coverageGroup;

    /**
     * @var bool
     */
    private $coverageEnabled = false;

    /**
     * @var string
     */
    private $defaultMinkSession;

    /**
     * @var string
     */
    private $minkSession;

    public function __construct(Mink $mink, $defaultMinkSession, $targetDirectory)
    {
        $this->mink = $mink;

        $this->defaultMinkSession = $defaultMinkSession;
        Assert::string($defaultMinkSession, 'Default Mink session should be a string');

        Assert::string($targetDirectory, 'Coverage target directory should be a string');
        $this->targetDirectory = $targetDirectory;
    }

    public static function getSubscribedEvents()
    {
        return [
            SuiteTested::BEFORE => 'beforeSuite',
            ScenarioTested::BEFORE => 'beforeScenario',
            SuiteTested::AFTER => 'afterSuite'
        ];
    }

    public function beforeSuite(BeforeSuiteTested $event)
    {
        $this->coverageEnabled = $event->getSuite()->hasSetting('remote_coverage_enabled')
            && $event->getSuite()->getSetting('remote_coverage_enabled');

        if (!$this->coverageEnabled) {
            return;
        }

        $this->minkSession = $event->getSuite()->hasSetting('mink_session') ?
            $event->getSuite()->getSetting('mink_session') : $this->defaultMinkSession;
        $this->coverageGroup = uniqid($event->getSuite()->getName(), true);
    }

    public function beforeScenario(ScenarioLikeTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $coverageId = $event->getFeature()->getFile() . ':' . $event->getNode()->getLine();

        $this->getMinkSession()->setCookie('collect_code_coverage', true);
        $this->getMinkSession()->setCookie('coverage_group', $this->coverageGroup);
        $this->getMinkSession()->setCookie('coverage_id', $coverageId);
    }

    public function afterSuite(AfterSuiteTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $driver = $this->getMinkSession()->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new \RuntimeException('Mink driver not supported');
        }

        $driver->getClient()->request(
            'GET',
            '/?export_code_coverage=true&coverage_group=' . urlencode($this->coverageGroup)
        );

        $coverage = unserialize($driver->getClient()->getResponse()->getContent());

        Storage::storeCodeCoverage($coverage, $this->targetDirectory, $event->getSuite()->getName());

        $this->reset();
    }

    private function reset()
    {
        $this->coverage = null;
        $this->coverageGroup = null;
        $this->coverageEnabled = false;
    }

    /**
     * @return Session
     */
    private function getMinkSession()
    {
        return $this->mink->getSession($this->minkSession);
    }
}

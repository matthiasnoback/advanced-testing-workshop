<?php
declare(strict_types=1);

namespace BehatRemoteCodeCoverage;

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

    public function __construct(Mink $mink, $targetDirectory)
    {
        $this->mink = $mink;
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

        $this->coverageGroup = uniqid($event->getSuite()->getName(), true);
    }

    public function beforeScenario(ScenarioLikeTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $coverageId = $event->getFeature()->getFile() . ':' . $event->getNode()->getLine();

        $this->mink->getSession('default')->setCookie('collect_code_coverage', true);
        $this->mink->getSession('default')->setCookie('coverage_group', $this->coverageGroup);
        $this->mink->getSession('default')->setCookie('coverage_id', $coverageId);
    }

    public function afterSuite(AfterSuiteTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        // TODO use Mink Session Driver
        $coverage = unserialize(
            file_get_contents('http://web:8080/?export_code_coverage=true&coverage_group=' . $this->coverageGroup)
        );

        Storage::storeCodeCoverage($coverage, $this->targetDirectory, $event->getSuite()->getName());

        $this->reset();
    }

    private function reset()
    {
        $this->coverage = null;
        $this->coverageGroup = null;
        $this->coverageEnabled = false;
    }
}

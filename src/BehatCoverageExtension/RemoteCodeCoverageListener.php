<?php
declare(strict_types=1);

namespace BehatCoverageExtension;

use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Mink\Mink;
use Behat\Testwork\EventDispatcher\Event\AfterSuiteTested;
use Behat\Testwork\EventDispatcher\Event\BeforeSuiteTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use LiveCodeCoverage\CodeCoverageFactory;
use SebastianBergmann\CodeCoverage\CodeCoverage;
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
    private $phpunitXmlPath;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var CodeCoverage
     */
    private $coverage;

    /**
     * @var string
     */
    private $testRunId;

    /**
     * @var bool
     */
    private $coverageEnabled = false;

    public function __construct(Mink $mink, $phpunitXmlPath, $targetDirectory)
    {
        $this->mink = $mink;
        $this->phpunitXmlPath = $phpunitXmlPath;
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
        $this->coverageEnabled = $event->getSuite()->getSetting('remote_coverage_enabled');
        if (!$this->coverageEnabled) {
            return;
        }

        $this->coverage = CodeCoverageFactory::createFromPhpUnitConfiguration($this->phpunitXmlPath);
        $this->testRunId = uniqid($event->getSuite()->getName(), true);
    }

    public function beforeScenario(ScenarioLikeTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        $coverageId = $event->getFeature()->getFile() . ':' . $event->getNode()->getLine();

        $this->mink->getSession('default')->setCookie('test_run_id', $this->testRunId);
        $this->mink->getSession('default')->setCookie('coverage_id', $coverageId);
    }

    public function afterSuite(AfterSuiteTested $event)
    {
        if (!$this->coverageEnabled) {
            return;
        }

        // TODO use Mink Session Driver
        $coverageData = unserialize(
            file_get_contents('http://web:8080/?export_code_coverage=true&test_run_id=' . $this->testRunId)
        );
        $this->coverage->merge($coverageData);

        // TODO delegate to utility
        $cov = '<?php return unserialize(' . var_export(serialize($this->coverage), true) . ');';
        $filePath = $this->targetDirectory . '/' . $event->getSuite()->getName() . '.cov';
        file_put_contents($filePath, $cov);

        $this->coverage = null;
    }
}

<?php
declare(strict_types=1);

use BehatCoverageExtension\RemoteCodeCoverage;
use Interop\Container\ContainerInterface;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Zend\Expressive\Application;

require __DIR__ . '/../vendor/autoload.php';

$coverageRoot = sys_get_temp_dir();

if (isset($_GET['export_code_coverage'], $_GET['test_run_id'])) {
    $codeCoverage = new CodeCoverage();

    $coverageDirectory = $coverageRoot . '/' . $_GET['test_run_id'];

    if (is_dir($coverageDirectory)) {
        $dir = new DirectoryIterator($coverageDirectory);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $partialCodeCoverage = include $fileInfo->getPathname();

            $codeCoverage->merge($partialCodeCoverage);
        }
    }

    echo serialize($codeCoverage);
    exit;
}

$remoteCodeCoverage = null;

if (getenv('COLLECT_CODE_COVERAGE') && isset($_COOKIE['test_run_id'], $_COOKIE['coverage_id'])) {
    $coverageDirectory = $coverageRoot . '/' . $_COOKIE['test_run_id'];
    $remoteCodeCoverage = RemoteCodeCoverage::bootstrap($coverageDirectory, __DIR__ . '/../phpunit.xml.dist', $_COOKIE['coverage_id']);
}

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../app/container.php';

/** @var Application $app */
$app = $container[Application::class];
$app->run();

if ($remoteCodeCoverage instanceof RemoteCodeCoverage) {
    $remoteCodeCoverage->stopAndSave();
}

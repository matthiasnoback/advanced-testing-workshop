<?php
declare(strict_types=1);

use BehatCoverageExtension\RemoteCodeCoverage;
use Interop\Container\ContainerInterface;
use LiveCodeCoverage\Storage;
use Zend\Expressive\Application;

require __DIR__ . '/../vendor/autoload.php';

$coverageRoot = sys_get_temp_dir();

if (isset($_GET['export_code_coverage'], $_GET['test_run_id'])) {
    $coverageDirectory = $coverageRoot . '/' . $_GET['test_run_id'];

    $codeCoverage = Storage::loadFromDirectory($coverageDirectory);

    header('Content-Type: text/plain');
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

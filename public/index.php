<?php
declare(strict_types=1);

use Interop\Container\ContainerInterface;
use LiveCodeCoverage\LiveCodeCoverage;
use Zend\Expressive\Application;

require __DIR__ . '/../vendor/autoload.php';

$shutDownCodeCoverage = LiveCodeCoverage::bootstrapRemoteCoverage(
    (bool)getenv('CODE_COVERAGE_ENABLED'),
    sys_get_temp_dir(),
    __DIR__ . '/../phpunit.xml.dist'
);

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../app/container.php';

/** @var Application $app */
$app = $container[Application::class];
$app->run();

$shutDownCodeCoverage();

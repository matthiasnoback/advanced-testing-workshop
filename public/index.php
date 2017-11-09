<?php
declare(strict_types = 1);

use Interop\Container\ContainerInterface;
use LiveCodeCoverage\LiveCodeCoverage;
use Zend\Expressive\Application;

require __DIR__ . '/../bootstrap.php';

if (getenv('COLLECT_CODE_COVERAGE')) {
    LiveCodeCoverage::bootstrap(
        __DIR__ . '/../var/coverage',
        __DIR__ . '/../phpunit.xml.dist'
    );
}

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../app/container.php';

/** @var Application $app */
$app = $container[Application::class];
$app->run();

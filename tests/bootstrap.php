<?php

use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Kernel as PrettyUrlsKernel;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

// needed to avoid encoding issues when running tests on different platforms
setlocale(\LC_ALL, 'en_US.UTF-8');

// needed to avoid failed tests when other timezones than UTC are configured for PHP
date_default_timezone_set('UTC');

// we want final classes in code but we need non-final classes in tests
// after trying many solutions (see https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/)
// none was reliable enough, so this custom solution removes the 'final' keyword
// from the source code of all project files (and restore it when tests finish)
// This has to be done BEFORE loading any PHP classes. Otherwise the changes in the
// source code contents are ignored
const EA_TEST_COMMENT_MARKER_START = '/* added-by-ea-tests';
const EA_TEST_COMMENT_MARKER_END = '*/';

foreach (glob(__DIR__.'/../src/**/*.php') as $sourceFilePath) {
    $sourceFilePath = realpath($sourceFilePath);
    $sourceFileContents = file_get_contents($sourceFilePath);
    $sourceFileContentsWithoutFinalClasses = preg_replace(
        '/^final class (.*)$/m',
        sprintf('%s final %s class \1', EA_TEST_COMMENT_MARKER_START, EA_TEST_COMMENT_MARKER_END),
        $sourceFileContents
    );
    file_put_contents($sourceFilePath, $sourceFileContentsWithoutFinalClasses);
}

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies using Composer to run the test suite.');
}
$autoload = require $file;

if ('1' === getenv('USE_PRETTY_URLS')) {
    $kernel = new PrettyUrlsKernel();
} else {
    $kernel = new Kernel();
}

// delete the existing cache directory to avoid issues
(new Symfony\Component\Filesystem\Filesystem())->remove($kernel->getCacheDir());

$application = new Application($kernel);
$application->setAutoExit(false);

$input = new ArrayInput(['command' => 'doctrine:database:drop', '--no-interaction' => true, '--force' => true]);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'doctrine:database:create', '--no-interaction' => true]);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'doctrine:schema:create']);
$application->run($input, new ConsoleOutput());

$input = new ArrayInput(['command' => 'doctrine:fixtures:load', '--no-interaction' => true, '--append' => false]);
$application->run($input, new ConsoleOutput());

unset($input, $application);

#!/usr/bin/env php
<?php

require_once __DIR__.'/../../../vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Process\Process;

$countryFlagIconsDir = __DIR__.'/../../../assets/icons/flags';

$fs = new Filesystem();

// clone the git repository with the flags in a temporary directory
$countryFlagsRepositoryUrl = 'https://gitlab.com/catamphetamine/country-flag-icons.git';
$countryFlagsTempDir = sys_get_temp_dir().'/country-flag-icons';
if (file_exists($countryFlagsTempDir)) {
    $fs->remove($countryFlagsTempDir);
    $fs->mkdir($countryFlagsTempDir);
}

$process = new Process(['git', 'clone', $countryFlagsRepositoryUrl, $countryFlagsTempDir]);
$process->run();

if (!$process->isSuccessful()) {
    echo 'Error cloning the country flags repository: '.$process->getErrorOutput();
    exit(1);
}

// prepare a temporary dir where the new flags will be stored (and later, this temporary dir will replace the original one)
$countryFlagIconsUpdatingDir = $countryFlagIconsDir.'-updating';
$fs->remove($countryFlagIconsUpdatingDir);
$fs->mkdir($countryFlagIconsUpdatingDir);

// to avoid any issues, we get the list of valid countries from Symfony itself
// and only copy the flags that are present in the Symfony list; any other flags are ignored
$validCountries = Countries::getCountryCodes();
foreach ($validCountries as $countryCode) {
    // the 3x2/ dir at the root of the repository contains the minified SVG flags
    $flagSvgFilePath = sprintf('%s/3x2/%s.svg', $countryFlagsTempDir, $countryCode);
    if (!file_exists($flagSvgFilePath)) {
        echo sprintf('Warning: the "%s" country code does not have a flag icon in the country flags repository.', $countryCode).\PHP_EOL;
        continue;
    }

    // these placeholders will be replaced by the real values when the SVG file is used in the Flag Twig component
    $flagSvgContent = file_get_contents($flagSvgFilePath);
    $flagSvgContent = str_replace(' viewBox=', ' class="country-flag" height="__HEIGHT__" viewBox=', $flagSvgContent);
    $flagSvgContent = str_replace('</svg>', '<title>__COUNTRY_NAME__</title></svg>', $flagSvgContent);

    file_put_contents($countryFlagIconsUpdatingDir.'/'.$countryCode.'.svg', $flagSvgContent);
}

$fs->remove($countryFlagIconsDir);
$fs->rename($countryFlagIconsUpdatingDir, $countryFlagIconsDir);

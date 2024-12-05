#!/usr/bin/env php
<?php

// format: 'old property that can't be used' => 'alternative logical property'
// see https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_logical_properties_and_values
$logicalCssProperties = [
    'border-bottom' => 'border-block-end',
    'border-bottom-color' => 'border-block-end-color',
    'border-bottom-left-radius' => 'border-end-start-radius',
    'border-bottom-right-radius' => 'border-end-end-radius',
    'border-bottom-style' => 'border-block-end-style',
    'border-bottom-width' => 'border-block-end-width',
    'border-left' => 'border-inline-start',
    'border-left-color' => 'border-inline-start-color',
    'border-left-style' => 'border-inline-start-style',
    'border-left-width' => 'border-inline-start-width',
    'border-right' => 'border-inline-end',
    'border-right-color' => 'border-inline-end-color',
    'border-right-style' => 'border-inline-end-style',
    'border-right-width' => 'border-inline-end-width',
    'border-top' => 'border-block-start',
    'border-top-color' => 'border-block-start-color',
    'border-top-left-radius' => 'border-start-start-radius',
    'border-top-right-radius' => 'border-start-end-radius',
    'border-top-style' => 'border-block-start-style',
    'border-top-width' => 'border-block-start-width',
    'bottom' => 'inset-block-end',
    'container-intrinsic-height' => 'contain-intrinsic-block-size',
    'container-intrinsic-width' => 'contain-intrinsic-inline-size',
    'height' => 'block-size',
    'left' => 'inset-inline-start',
    'margin-bottom' => 'margin-block-end',
    'margin-left' => 'margin-inline-start',
    'margin-right' => 'margin-inline-end',
    'margin-top' => 'margin-block-start',
    'max-height' => 'max-block-size',
    'max-width' => 'max-inline-size',
    'min-height' => 'min-block-size',
    'min-width' => 'min-inline-size',
    'overscroll-behavior-x' => 'overscroll-behavior-inline',
    'overscroll-behavior-y' => 'overscroll-behavior-block',
    'overflow-x' => 'overflow-inline',
    'overflow-y' => 'overflow-block',
    'padding-bottom' => 'padding-block-end',
    'padding-left' => 'padding-inline-start',
    'padding-right' => 'padding-inline-end',
    'padding-top' => 'padding-block-start',
    'right' => 'inset-inline-end',
    'top' => 'inset-block-start',
    'width' => 'inline-size',
];

$directory = __DIR__.'/../../../assets';

return lintCssFiles($directory, $logicalCssProperties);

function lintCssFiles($directory, $logicalCssProperties): int
{
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $cssFiles = new RegexIterator($files, '/\.css$/');

    $numErrors = 0;
    foreach ($cssFiles as $file) {
        $numErrors += lintFileCssProperties($file, $logicalCssProperties);
    }

    if ($numErrors > 0) {
        echo sprintf("Found %d issues in %d CSS files\n", $numErrors, iterator_count($cssFiles));

        return 1;
    }

    echo sprintf("No issues found in %d CSS files\n", iterator_count($cssFiles));

    return 0;
}

function lintFileCssProperties($file, $logicalCssProperties): int
{
    $numErrors = 0;
    $lines = file($file);
    $insideMediaQuery = false;

    foreach ($lines as $lineNumber => $line) {
        if (str_contains($line, '@media')) {
            $insideMediaQuery = true;
        }
        if ($insideMediaQuery && str_contains($line, '}')) {
            $insideMediaQuery = false;
        }

        foreach ($logicalCssProperties as $forbidden => $alternative) {
            // some old CSS properties can still be used because e.g. media queries don't support logical properties
            if ($insideMediaQuery && \in_array($forbidden, ['max-height', 'min-height', 'max-width', 'min-width'], true)) {
                continue;
            }

            $pattern = '/(?<!-)\b'.preg_quote($forbidden, '/').'\s*:/';
            if (preg_match($pattern, $line)) {
                echo sprintf("File: %s\n", $file);
                echo sprintf("Line: %d\n", $lineNumber + 1);
                echo sprintf("Issue: you can't use the property '%s' because it's not safe for different writing systems; use instead the logical property '%s'\n", $forbidden, $alternative);
                echo sprintf("CSS code: %s\n\n", trim($line));

                ++$numErrors;
            }
        }
    }

    return $numErrors;
}

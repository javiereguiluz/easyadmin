#!/usr/bin/env php
<?php

$manifestJsonPath = __DIR__.'/../../../public/manifest.json';
if (!file_exists($manifestJsonPath)) {
    return 0;
}

fixManifestJsonEntriesPaths($manifestJsonPath);
fixFontPathsInCssFiles($manifestJsonPath);

// We use a named Symfony Asset package (see src/Asset/AssetPackage) to manage
// the versioned assets of this project. In order to allow using EasyAdmin in
// applications served in subdirectories (e.g. example.com/foo/admin) we use a
// PathPackage to generate the right URLs for assets.
// This means that asset paths in 'manifest.json' should not contain any path:
//   'app.css' => 'app.1e1ba55d.css'
//
// Sadly, even is setPublicPath() in our webpack.config.js is empty, the generated
// 'manifest.json' adds leading slashes in all paths, which breaks PathPackage:
//   'app.css' => '/app.1e1ba55d.css'
//
// This function removes that leading slash from all asset paths.
function fixManifestJsonEntriesPaths(string $manifestJsonPath)
{
    $manifestJsonContents = json_decode(file_get_contents($manifestJsonPath), associative: true, flags: \JSON_THROW_ON_ERROR);
    $fixedManifestJsonContents = [];
    foreach ($manifestJsonContents as $assetName => $assetPath) {
        $assetPath = ltrim($assetPath, '/');
        $fixedManifestJsonContents[$assetName] = $assetPath;
    }

    $newJsonManifestContents = json_encode($fixedManifestJsonContents, flags: \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    // the original manifest.json file uses a 2 white space indentation, so keep that
    $newJsonManifestContents = str_replace('    ', '  ', $newJsonManifestContents);
    file_put_contents($manifestJsonPath, $newJsonManifestContents);
}

// In the generated CSS files, there are styles like this:
// @font-face{font-family:FontAwesome;src:url(bundles/easyadmin/fonts/fa-v4compatibility.afac8956.woff2) format("woff2")
// This doesn't work when serving the Symfony application in a subdirectory.
// We need something like this instead:
// @font-face{font-family:FontAwesome;src:url(fonts/fa-v4compatibility.afac8956.woff2) format("woff2")
// There might be some way of doing this in Webpack Encore, but I can't find it, so let's be pragmatic.
function fixFontPathsInCssFiles(string $manifestJsonPath)
{
    $manifestJsonContents = json_decode(file_get_contents($manifestJsonPath), associative: true, flags: \JSON_THROW_ON_ERROR);
    foreach ($manifestJsonContents as $assetName => $assetPath) {
        if (!str_ends_with($assetPath, '.css')) {
            continue;
        }

        $assetFilePath = __DIR__.'/../../../public/'.str_replace('bundles/easyadmin/', '', $assetPath);
        $originalFileContents = file_get_contents($assetFilePath);
        $fixedFileContents = str_replace('url(/fonts/', 'url(fonts/', $originalFileContents);

        file_put_contents($assetFilePath, $fixedFileContents);
    }
}

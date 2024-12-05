<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Asset;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This defines a Symfony Asset named package that groups all the assets provided
 * by EasyAdmin. This is needed because EasyAdmin uses asset versioning, so the
 * full absolute URLs of assets isn't known (the URL contain changing hashes).
 *
 * In practice this uses the same strategy (and even the same "manifest.json" file)
 * used by Webpack Encore. We do this because we want to keep EasyAdmin dependencies as
 * lean as possible, so we don't want to require Webpack Encore to use EasyAdmin.
 */
final class AssetPackage implements PackageInterface
{
    public const PACKAGE_NAME = 'easyadmin.assets.package';

    private PackageInterface $package;

    public function __construct(RequestStack $requestStack)
    {
        $this->package = new PathPackage(
            '/bundles/easyadmin',
            new JsonManifestVersionStrategy(__DIR__.'/../../public/manifest.json'),
            new RequestStackContext($requestStack)
        );
    }

    public function getUrl(string $assetPath): string
    {
        return $this->package->getUrl($assetPath);
    }

    public function getVersion(string $assetPath): string
    {
        return $this->package->getVersion($assetPath);
    }
}

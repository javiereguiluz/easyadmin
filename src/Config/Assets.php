<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class Assets
{
    private AssetsDto $dto;

    private function __construct(AssetsDto $assetsDto)
    {
        $this->dto = $assetsDto;
    }

    public static function new(): self
    {
        $dto = new AssetsDto();

        return new self($dto);
    }

    public function addWebpackEncoreEntry(Asset|string $entryNameOrAsset): self
    {
        if (!class_exists('Symfony\\WebpackEncoreBundle\\WebpackEncoreBundle')) {
            throw new \RuntimeException(sprintf('You are trying to add a Webpack Encore entry called "%s" but WebpackEncoreBundle is not installed in your project. Try running "composer require symfony/webpack-encore-bundle"', $entryNameOrAsset));
        }

        if (\is_string($entryNameOrAsset)) {
            $this->dto->addWebpackEncoreAsset(new AssetDto($entryNameOrAsset));
        } else {
            $this->dto->addWebpackEncoreAsset($entryNameOrAsset->getAsDto());
        }

        return $this;
    }

    public function addAssetMapperEntry(Asset|string ...$entryNameOrAsset): self
    {
        if (!class_exists('Symfony\\Component\\AssetMapper\\AssetMapper')) {
            $names = array_map(static fn (Asset|string $nameOrAsset) => \is_string($nameOrAsset) ? '"'.$nameOrAsset.'"' : '"'.$nameOrAsset->getAsDto()->getValue().'"', $entryNameOrAsset);

            throw new \RuntimeException(sprintf('You are trying to add '.(1 === \count($names) ? 'an AssetMapper entry called %s' : ' some AssetMapper entries (%s)').' but the AssetMapper component is not installed in your project. Try running "composer require symfony/asset-mapper"', implode(', ', $names)));
        }

        foreach ($entryNameOrAsset as $nameOrAsset) {
            if (\is_string($nameOrAsset)) {
                $this->dto->addAssetMapperAsset(new AssetDto($nameOrAsset));
            } else {
                $this->dto->addAssetMapperAsset($nameOrAsset->getAsDto());
            }
        }

        return $this;
    }

    public function addCssFile(Asset|string $pathOrAsset): self
    {
        if (\is_string($pathOrAsset)) {
            $this->dto->addCssAsset(new AssetDto($pathOrAsset));
        } else {
            $this->dto->addCssAsset($pathOrAsset->getAsDto());
        }

        return $this;
    }

    public function addJsFile(Asset|string $pathOrAsset): self
    {
        if (\is_string($pathOrAsset)) {
            $this->dto->addJsAsset(new AssetDto($pathOrAsset));
        } else {
            $this->dto->addJsAsset($pathOrAsset->getAsDto());
        }

        return $this;
    }

    public function addHtmlContentToHead(string $htmlContent): self
    {
        $this->dto->addHtmlContentToHead($htmlContent);

        return $this;
    }

    public function addHtmlContentToBody(string $htmlContent): self
    {
        $this->dto->addHtmlContentToBody($htmlContent);

        return $this;
    }

    public function useCustomIconSet(string $defaultIconPrefix = ''): self
    {
        if (str_contains($defaultIconPrefix, ':') || str_contains(trim($defaultIconPrefix), ' ')) {
            throw new \InvalidArgumentException(sprintf('The default icon prefix cannot contain spaces or the ":" character ("%s" given).', $defaultIconPrefix));
        }

        $this->dto->setIconSet(IconSet::Custom);
        $this->dto->setDefaultIconPrefix(trim($defaultIconPrefix));

        return $this;
    }

    public function getAsDto(): AssetsDto
    {
        return $this->dto;
    }
}

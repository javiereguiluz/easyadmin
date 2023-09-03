<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\CrudInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

final class AssetDto implements AssetDtoInterface
{
    private string $value;
    // the Symfony Asset package name which this asset belongs to
    private ?string $packageName = null;
    private bool $async = false;
    private bool $defer = false;
    private bool $preload = false;
    private bool $nopush = false;
    private ?string $webpackPackageName = null;
    private string $webpackEntrypointName = '_default';
    private array $htmlAttributes = [];
    private KeyValueStore $loadedOn;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->loadedOn = KeyValueStore::new([
            CrudInterface::PAGE_INDEX => CrudInterface::PAGE_INDEX,
            CrudInterface::PAGE_DETAIL => CrudInterface::PAGE_DETAIL,
            CrudInterface::PAGE_EDIT => CrudInterface::PAGE_EDIT,
            CrudInterface::PAGE_NEW => CrudInterface::PAGE_NEW,
        ]);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPackageName(): ?string
    {
        return $this->packageName;
    }

    public function setPackageName(string $packageName): void
    {
        $this->packageName = $packageName;
    }

    public function setAsync(bool $async): void
    {
        $this->async = $async;
    }

    public function isAsync(): bool
    {
        return $this->async;
    }

    public function setDefer(bool $defer): void
    {
        $this->defer = $defer;
    }

    public function isDefer(): bool
    {
        return $this->defer;
    }

    public function setPreload(bool $preload): void
    {
        $this->preload = $preload;
    }

    public function isPreload(): bool
    {
        return $this->preload;
    }

    public function setNopush(bool $nopush): void
    {
        $this->nopush = $nopush;
    }

    public function isNopush(): bool
    {
        return $this->nopush;
    }

    public function setWebpackPackageName(?string $packageName): void
    {
        $this->webpackPackageName = $packageName;
    }

    public function getWebpackPackageName(): ?string
    {
        return $this->webpackPackageName;
    }

    public function setWebpackEntrypointName(string $entrypointName): void
    {
        $this->webpackEntrypointName = $entrypointName;
    }

    public function getWebpackEntrypointName(): string
    {
        return $this->webpackEntrypointName;
    }

    public function setHtmlAttribute(string $attrName, string $attrValue): void
    {
        $this->htmlAttributes[$attrName] = $attrValue;
    }

    public function getHtmlAttributes(): array
    {
        return $this->htmlAttributes;
    }

    public function getLoadedOn(): KeyValueStore
    {
        return $this->loadedOn;
    }

    public function setLoadedOn(KeyValueStore $loadedOn): void
    {
        $this->loadedOn = $loadedOn;
    }
}

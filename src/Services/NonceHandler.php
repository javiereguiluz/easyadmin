<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Services;

final class NonceHandler
{
    /** @var CspNonceGeneratorInterface|null */
    private $generator;

    public function __construct(?CspNonceGeneratorInterface $generator = null)
    {
        $this->generator = $generator;
    }

    public function hasGenerator(): bool
    {
        return null !== $this->generator;
    }

    public function getGenerator(): CspNonceGeneratorInterface
    {
        return $this->generator;
    }
}

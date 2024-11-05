<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ScopesDto;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class Scopes
{
    private ScopesDto $dto;

    private function __construct(ScopesDto $scopesDto)
    {
        $this->dto = $scopesDto;
    }

    public static function new(): self
    {
        $dto = new ScopesDto();

        return new self($dto);
    }

    public function addScope(Scope $scope): self
    {
        if (null !== $this->dto->getScopeByName($scope->getName())) {
            throw new \InvalidArgumentException(sprintf('There are two or more different scopes with the same name "%s".', $scope->getName()));
        }

        $this->dto->addScope($scope);

        return $this;
    }

    public function getAsDto(): ScopesDto
    {
        return $this->dto;
    }
}

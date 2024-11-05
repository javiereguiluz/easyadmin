<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ScopeDto;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class Scope
{
    private ScopeDto $dto;

    private function __construct(ScopeDto $scopeDto)
    {
        $this->dto = $scopeDto;
    }

    public static function new(string $scopeName): self
    {
        return (new self(new ScopeDto()))
            ->setName($scopeName);
    }

    public function setName(string $name): self
    {
        $this->dto->setName($name);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->dto->getName();
    }

    public function addButton(ScopeButton $button): self
    {
        if ($this->dto->hasButtonWithName($button->getName())) {
            throw new \InvalidArgumentException(sprintf('There are two or more different buttons with the same name "%s".', $button->getName()));
        }

        $this->dto->addButton($button->getAsDto());

        return $this;
    }

    public function setDefault(string $Name): self
    {
        if (!$this->dto->hasButtonWithName($Name)) {
            throw new \InvalidArgumentException(sprintf('There are no button with name "%s".', $Name));
        }

        $this->dto->setDefaultButton($Name);

        return $this;
    }

    public function getAsDto(): ScopeDto
    {
        return $this->dto;
    }
}

<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class ScopeDto
{
    private ?string $name = null;
    private ?string $defaultButton = null;
    /** @var array<string,ScopeButtonDto> */
    private array $buttons = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDefaultButton(): ?string
    {
        return $this->defaultButton;
    }

    public function setDefaultButton(?string $defaultButton): self
    {
        $this->defaultButton = $defaultButton;

        return $this;
    }

    /** @return array<string,ScopeButtonDto> */
    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function setButtons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function hasButtonWithName(string $name)
    {
        return \in_array($name, array_keys($this->getButtons()), true);
    }

    public function addButton(ScopeButtonDto $button): self
    {
        $this->buttons[$button->getName()] = $button;

        return $this;
    }

    public function findButton(string $name): ?ScopeButtonDto
    {
        return $this->buttons[$name] ?? null;
    }
}

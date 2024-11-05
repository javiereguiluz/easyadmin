<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\Scope;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class ScopesDto
{
    /**
     * @param array<string, ScopeDto> $scopes
     */
    private array $scopes = [];

    /**
     * @return array<string, ScopeDto>
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function addScope(Scope $scope)
    {
        $this->scopes[$scope->getName()] = $scope->getAsDto();
    }

    public function getScopeByName(string $name): ?ScopeDto
    {
        return $this->scopes[$name] ?? null;
    }

    public function processRequest(Request $request)
    {
        $scopesParams = $request->query->all()[EA::SCOPE] ?? [];
        foreach ($this->scopes as $scopeName => $scope) {
            $buttonName = $scopesParams[$scopeName] ?? $scope->getDefaultButton();
            $button = null !== $buttonName ? $scope->findButton($buttonName) : null;
            if (null !== $button) {
                foreach ($button->getFilters() as $filter) {
                    $filter->updateRequest($request);
                }
                $button->setActive(true);
            }
        }
    }
}

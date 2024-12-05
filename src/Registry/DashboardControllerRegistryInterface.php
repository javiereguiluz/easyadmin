<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Registry;

interface DashboardControllerRegistryInterface
{
    public function getControllerFqcnByContextId(string $contextId): ?string;

    public function getContextIdByControllerFqcn(string $controllerFqcn): ?string;

    public function getControllerFqcnByRoute(string $routeName): ?string;

    public function getRouteByControllerFqcn(string $controllerFqcn): ?string;

    public function getNumberOfDashboards(): int;

    public function getFirstDashboardRoute(): ?string;

    public function getFirstDashboardFqcn(): ?string;

    /**
     * @return array<int, array{controller: string, route: string, context: string}>
     */
    public function getAll(): array;
}

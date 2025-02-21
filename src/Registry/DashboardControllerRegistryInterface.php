<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Registry;

/**
 * @deprecated since 4.25.0, will be removed in EasyAdmin 5.0.0. This registry is generally not needed by developers in newer versions of EasyAdmin.
 */
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

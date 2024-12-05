<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Orm;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Orm\EntityPaginatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\PaginatorDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityPaginator implements EntityPaginatorInterface
{
    private AdminUrlGeneratorInterface $adminUrlGenerator;
    private EntityFactory $entityFactory;
    private RequestStack $requestStack;
    private ?int $currentPage = null;
    private ?int $pageSize = null;
    private ?int $rangeSize = null;
    private ?int $rangeEdgeSize = null;
    private $results;
    private $numResults;
    private ?int $rangeFirstResultNumber = null;
    private ?int $rangeLastResultNumber = null;

    public function __construct(AdminUrlGeneratorInterface $adminUrlGenerator, EntityFactory $entityFactory, RequestStack $requestStack)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityFactory = $entityFactory;
        $this->requestStack = $requestStack;
    }

    public function paginate(PaginatorDto $paginatorDto, QueryBuilder $queryBuilder): EntityPaginatorInterface
    {
        $this->pageSize = $paginatorDto->getPageSize();
        $this->rangeSize = $paginatorDto->getRangeSize();
        $this->rangeEdgeSize = $paginatorDto->getRangeEdgeSize();
        $this->currentPage = max(1, $paginatorDto->getPageNumber());
        $firstResult = ($this->currentPage - 1) * $this->pageSize;
        $this->rangeFirstResultNumber = $this->pageSize * ($this->currentPage - 1) + 1;
        $this->rangeLastResultNumber = $this->rangeFirstResultNumber + $this->pageSize - 1;

        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        if (0 === \count($queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new Paginator($query, $paginatorDto->fetchJoinCollection());

        if (null === $useOutputWalkers = $paginatorDto->useOutputWalkers()) {
            $havingPart = $queryBuilder->getDQLPart('having');
            if (\is_array($havingPart)) {
                $useOutputWalkers = \count($havingPart) > 0;
            } else {
                $useOutputWalkers = null !== $havingPart;
            }
        }
        $paginator->setUseOutputWalkers($useOutputWalkers);

        $this->results = $paginator->getIterator();
        $this->numResults = $paginator->count();
        if ($this->rangeLastResultNumber > $this->numResults) {
            $this->rangeLastResultNumber = $this->numResults;
        }

        return $this;
    }

    public function generateUrlForPage(int $page): string
    {
        $pageUrl = $this->adminUrlGenerator->set(EA::PAGE, $page);

        $currentRequest = $this->requestStack->getCurrentRequest();
        $usesPrettyUrls = null !== $crudControllerFqcn = $currentRequest->attributes->get(EA::CRUD_CONTROLLER_FQCN);
        if ($usesPrettyUrls) {
            $pageUrl->setController($crudControllerFqcn)->setAction($currentRequest->attributes->get(EA::CRUD_ACTION));
        }

        return $pageUrl->generateUrl();
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    /**
     * It returns the closest available pages around the current page.
     * E.g. a paginator with 35 pages, if current page = 1, returns [1, 2, 3, 4, null, 35]
     *      if current page = 18, returns [1, null, 15, 16, 17, 18, 19, 20, 21, null, 35]
     * NULL values mean a gap in the pagination (they can be represented as ellipsis in the templates).
     *
     * This code was inspired by https://github.com/django/django/blob/55fabc53373a8c7ef31d8c4cffd2a07be0a88c2e/django/core/paginator.py#L134
     * (c) Django Project
     *
     * @return iterable<int|null>
     */
    public function getPageRange(?int $pagesOnEachSide = null, ?int $pagesOnEdges = null): iterable
    {
        $pagesOnEachSide = $pagesOnEachSide ?? $this->rangeSize;
        $pagesOnEdges = $pagesOnEdges ?? $this->rangeEdgeSize;

        if (0 === $pagesOnEachSide) {
            return null;
        }

        if ($this->getLastPage() <= ($pagesOnEachSide + $pagesOnEdges) * 2) {
            return yield from range(1, $this->getLastPage());
        }

        if ($this->getCurrentPage() > ($pagesOnEachSide + $pagesOnEdges + 1)) {
            yield from range(1, $pagesOnEdges);
            yield null;
            yield from range($this->getCurrentPage() - $pagesOnEachSide, $this->getCurrentPage());
        } else {
            yield from range(1, $this->getCurrentPage());
        }

        if ($this->getCurrentPage() < ($this->getLastPage() - $pagesOnEachSide - $pagesOnEdges - 1)) {
            yield from range($this->getCurrentPage() + 1, $this->getCurrentPage() + $pagesOnEachSide);
            yield null;
            yield from range($this->getLastPage() - $pagesOnEdges + 1, $this->getLastPage());
        } elseif ($this->getCurrentPage() + 1 <= $this->getLastPage()) {
            yield from range($this->getCurrentPage() + 1, $this->getLastPage());
        }
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    public function isOutOfRange(): bool
    {
        if (1 === $this->currentPage) {
            return false;
        }

        return $this->currentPage < 1 || $this->currentPage > $this->getLastPage();
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getResults(): ?iterable
    {
        return $this->results;
    }

    /*
     * Returns the result number (e.g. 21) of the first value
     * included in the current results range. It's useful to
     * display a message like: "Showing 21 to 40 of 135 entries"
     */
    public function getRangeFirstResultNumber(): int
    {
        return $this->rangeFirstResultNumber;
    }

    /*
     * Returns the result number (e.g. 40) of the last value
     * included in the current results range. It's useful to
     * display a message like: "Showing 21 to 40 of 135 entries"
     */
    public function getRangeLastResultNumber(): int
    {
        return $this->rangeLastResultNumber;
    }

    public function getResultsAsJson(): string
    {
        $jsonResult = [];
        foreach ($this->getResults() ?? [] as $entityInstance) {
            $entityDto = $this->entityFactory->createForEntityInstance($entityInstance);

            $jsonResult['results'][] = [
                EA::ENTITY_ID => $entityDto->getPrimaryKeyValueAsString(),
                'entityAsString' => $entityDto->toString(),
            ];
        }

        $nextPageUrl = !$this->hasNextPage() ? null : $this->adminUrlGenerator->set(EA::PAGE, $this->getNextPage());
        $currentRequest = $this->requestStack->getCurrentRequest();
        $usesPrettyUrls = null !== $crudControllerFqcn = $currentRequest->attributes->get(EA::CRUD_CONTROLLER_FQCN);
        if ($usesPrettyUrls && null !== $nextPageUrl) {
            $nextPageUrl->setController($crudControllerFqcn)->setAction($currentRequest->attributes->get(EA::CRUD_ACTION));
        }

        $jsonResult['next_page'] = $nextPageUrl?->generateUrl();

        return json_encode($jsonResult, \JSON_THROW_ON_ERROR);
    }
}

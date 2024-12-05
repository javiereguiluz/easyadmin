<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SearchMode;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class SearchDto
{
    private Request $request;
    private array $defaultSort;
    private array $customSort;
    /** @internal */
    private ?array $cachedSortConfig = null;
    private string $query;
    /** @var string[]|null */
    private ?array $searchableProperties;
    /** @var string[]|null */
    private ?array $appliedFilters;
    private string $searchMode;

    public function __construct(Request $request, ?array $searchableProperties, ?string $query, array $defaultSort, array $customSort, ?array $appliedFilters, string $searchMode = SearchMode::ALL_TERMS)
    {
        $this->request = $request;
        $this->searchableProperties = $searchableProperties;
        $this->query = trim((string) $query);
        $this->defaultSort = $defaultSort;
        $this->customSort = $customSort;
        $this->appliedFilters = $appliedFilters;
        $this->searchMode = $searchMode;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getSort(): array
    {
        if (null !== $this->cachedSortConfig) {
            return $this->cachedSortConfig;
        }

        // we can't use an array_merge() call because $customSort has more priority
        // than $defaultSort, so the default sort must only be applied if there's
        // not already a custom sort config for the same field
        $mergedSort = $this->customSort;
        foreach ($this->defaultSort as $fieldName => $order) {
            if (!\array_key_exists($fieldName, $mergedSort)) {
                $mergedSort[$fieldName] = $order;
            }
        }

        return $this->cachedSortConfig = $mergedSort;
    }

    public function isSortingField(string $fieldProperty): bool
    {
        $firstSortField = \count($this->getSort()) > 0 ? array_keys($this->getSort())[0] : null;
        if (null === $firstSortField) {
            return false;
        }

        // TODO: check for association properties when they support search (e.g. 'user.name')
        return $fieldProperty === $firstSortField;
    }

    public function getSortDirection(string $fieldProperty): string
    {
        return \array_key_exists($fieldProperty, $this->getSort()) ? $this->getSort()[$fieldProperty] : 'DESC';
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Splits the query search string into a set of terms to search, taking into
     * account that quoted strings must be considered as a single term.
     * For example:
     *  'foo bar' => ['foo', 'bar']
     *  'foo "bar baz" qux' => ['foo', 'bar baz', 'qux'].
     */
    public function getQueryTerms(): array
    {
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $this->query, $matches);
        $terms = array_map(static fn ($match) => trim($match, '" '), $matches[0]);

        return $terms;
    }

    /**
     * @return string[]|null
     */
    public function getSearchableProperties(): ?array
    {
        return $this->searchableProperties;
    }

    public function getAppliedFilters(): ?array
    {
        return $this->appliedFilters;
    }

    public function getSearchMode(): string
    {
        return $this->searchMode;
    }
}

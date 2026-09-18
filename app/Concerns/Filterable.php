<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Filterable
{
    protected function getSearchableColumns(): array
    {
        return property_exists($this, 'searchable') && is_array($this->searchable)
            ? $this->searchable
            : ['name', 'created_at'];
    }

    private function parseFilters(array $filters, array $searchable = []): array
    {
        $columns = ! empty($searchable) ? $searchable : $this->getSearchableColumns();
        $defaultSortBy = $columns[0] ?? 'created_at';
        $hasSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class), true);

        return [
            'columns' => $columns,
            'sort_by' => in_array($sort = $filters['sort_by'] ?? $defaultSortBy, $columns, true) ? $sort : $defaultSortBy,
            'sort_dir' => in_array($dir = strtolower($filters['sort_dir'] ?? 'asc'), ['asc', 'desc'], true) ? $dir : 'asc',
            'search' => trim($filters['search'] ?? ''),
            'per_page' => (int) ($filters['per_page'] ?? 10),
            'page' => (int) ($filters['page'] ?? 1),
            'has_soft_deletes' => $hasSoftDeletes,
            'trashed' => $hasSoftDeletes && filter_var($filters['trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    public function scopeFilter(Builder $query, array $filters = [], array $searchable = []): Builder
    {
        $parsed = $this->parseFilters($filters, $searchable);

        if ($parsed['trashed']) {
            $query->onlyTrashed();
        }

        $query->when($parsed['search'], function (Builder $query) use ($parsed) {
            $query->where(function (Builder $subQuery) use ($parsed) {
                foreach ($parsed['columns'] as $column) {
                    $subQuery->orWhere($column, 'like', "%{$parsed['search']}%");
                }
            });
        });

        return $query->orderBy($parsed['sort_by'], $parsed['sort_dir']);
    }

    public function scopeFilterPaginate(Builder $query, array $filters = [], array $searchable = []): array
    {
        $parsed = $this->parseFilters($filters, $searchable);

        $query = $this->scopeFilter($query, $filters, $searchable);
        $paginator = $query->paginate($parsed['per_page'], ['*'], 'page', $parsed['page']);

        $filterPayload = [
            'search' => $parsed['search'],
            'sort_by' => $parsed['sort_by'],
            'sort_dir' => $parsed['sort_dir'],
        ];

        if ($parsed['has_soft_deletes']) {
            $filterPayload['trashed'] = $parsed['trashed'];
        }

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'filters' => $filterPayload,
        ];
    }
}

<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasDataGrid
{
    /**
     * Apply data grid filters, sorting, and return paginated results.
     */
    public function scopeApplyDataGrid(Builder $query, Request $request): Builder
    {
        return $query
            ->applyGridFilters($request)
            ->applyGridSearch($request)
            ->applyGridSorting($request);
    }

    /**
     * Apply filters based on the model's gridFilterable configuration.
     */
    public function scopeApplyGridFilters(Builder $query, Request $request): Builder
    {
        $filterableColumns = $this->getGridFilterable();

        foreach ($filterableColumns as $key => $config) {
            $config = is_array($config) ? $config : ['type' => $config];
            $type = $config['type'] ?? 'exact';
            $requestKey = $config['request_key'] ?? $key;
            $column = $config['column'] ?? $key;

            if (! $request->filled($requestKey)) {
                continue;
            }

            $value = $request->input($requestKey);

            match ($type) {
                'exact' => $query->where($column, $value),
                'like' => $query->where($column, 'like', "%{$value}%"),
                'date_from' => $query->whereDate($column, '>=', $value),
                'date_to' => $query->whereDate($column, '<=', $value),
                'relationship' => $this->applyRelationshipFilter($query, $config, $value),
                default => $query->where($column, $value),
            };
        }

        return $query;
    }

    /**
     * Apply relationship filter.
     */
    protected function applyRelationshipFilter(Builder $query, array $config, mixed $value): void
    {
        if (isset($config['relation']) && isset($config['relation_column'])) {
            $query->whereHas($config['relation'], function ($q) use ($config, $value) {
                $q->where($config['relation_column'], 'like', "%{$value}%");
            });
        }
    }

    /**
     * Apply global search based on the model's gridSearchable configuration.
     */
    public function scopeApplyGridSearch(Builder $query, Request $request): Builder
    {
        if (! $request->filled('search')) {
            return $query;
        }

        $search = $request->input('search');
        $searchableColumns = $this->getGridSearchable();

        if (empty($searchableColumns)) {
            return $query;
        }

        $query->where(function ($q) use ($searchableColumns, $search) {
            foreach ($searchableColumns as $column => $config) {
                if (is_numeric($column)) {
                    $q->orWhere($config, 'like', "%{$search}%");
                } elseif (is_array($config) && isset($config['relation'], $config['column'])) {
                    $q->orWhereHas($config['relation'], function ($subQuery) use ($config, $search) {
                        $subQuery->where($config['column'], 'like', "%{$search}%");
                    });
                }
            }
        });

        return $query;
    }

    /**
     * Apply sorting based on the model's gridSortable configuration.
     */
    public function scopeApplyGridSorting(Builder $query, Request $request): Builder
    {
        $sortColumn = $request->input('sort_column', $this->getDefaultSortColumn());
        $sortDirection = $request->input('sort_direction', $this->getDefaultSortDirection());

        $allowedSortColumns = $this->getGridSortable();

        if (in_array($sortColumn, $allowedSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy($this->getDefaultSortColumn(), $this->getDefaultSortDirection());
        }

        return $query;
    }

    /**
     * Get paginated grid results.
     */
    public function scopePaginateForGrid(Builder $query, Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $perPage = min((int) $request->input('per_page', 10), 100);
        $page = (int) $request->input('current_page', 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get filterable columns configuration.
     * Override in model to define filterable columns.
     *
     * @return array<string, string|array>
     */
    public function getGridFilterable(): array
    {
        return property_exists($this, 'gridFilterable') ? $this->gridFilterable : [];
    }

    /**
     * Get sortable columns.
     * Override in model to define sortable columns.
     *
     * @return array<string>
     */
    public function getGridSortable(): array
    {
        return property_exists($this, 'gridSortable') ? $this->gridSortable : ['id', 'created_at'];
    }

    /**
     * Get searchable columns configuration.
     * Override in model to define searchable columns.
     *
     * @return array<int|string, string|array>
     */
    public function getGridSearchable(): array
    {
        return property_exists($this, 'gridSearchable') ? $this->gridSearchable : [];
    }

    /**
     * Get default sort column.
     */
    public function getDefaultSortColumn(): string
    {
        return property_exists($this, 'gridDefaultSortColumn') ? $this->gridDefaultSortColumn : 'created_at';
    }

    /**
     * Get default sort direction.
     */
    public function getDefaultSortDirection(): string
    {
        return property_exists($this, 'gridDefaultSortDirection') ? $this->gridDefaultSortDirection : 'desc';
    }

    /**
     * Format paginated results for JSON response.
     */
    public static function formatGridResponse(\Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}

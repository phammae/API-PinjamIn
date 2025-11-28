<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class QueryFilterHelper
{
    /**
     * Apply filters to query builder
     *
     * @param Builder $query
     * @param array $filters
     * @param array $searchColumns - kolom yang bisa di-search
     * @return Builder
     */
    public static function applyFilters(Builder $query, array $filters, array $searchColumns = []): Builder
    {
        // Filter by published status
        if (isset($filters['published']) && $filters['published'] !== '') {
            $published = $filters['published'];

            if (is_string($published)) {
                $published = filter_var($published, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            } elseif (is_numeric($published)) {
                $published = (bool) $published;
            }

            if ($published !== null) {
                $query->where('published', $published ? 1 : 0);
            }
        }

        // Filter by status (generic)
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Search filter
        if (isset($filters['search']) && $filters['search'] !== '') {
            $searchTerm = $filters['search'];

            $query->where(function ($q) use ($searchTerm, $searchColumns) {
                if (empty($searchColumns)) {
                    // Default search columns jika tidak didefinisikan
                    $searchColumns = ['title', 'name', 'description'];
                }

                foreach ($searchColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', "%{$searchTerm}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$searchTerm}%");
                    }
                }
            });
        }

        //Handle search relasi
        if (isset($filters['relations']) && !empty($filters['relations'])) {
            foreach ($filters['relations'] as $relation => $columns) {
                $query->orWhereHas($relation, function ($q) use ($columns, $searchTerm) {
                    foreach ($columns as $col) {
                        $q->where($col, 'like', "%{$searchTerm}%");
                    }
                });
            }
        }

        // Date range filters
        if (isset($filters['date_from']) && $filters['date_from'] !== '') {
            $query->where('created_at', '>=', self::parseDateInput($filters['date_from'], false));
        }

        if (isset($filters['date_to']) && $filters['date_to'] !== '') {
            $query->where('created_at', '<=', self::parseDateInput($filters['date_to'], true));
        }

        // Filter by specific date
        if (isset($filters['date']) && $filters['date'] !== '') {
            $query->whereDate('created_at', $filters['date']);
        }

        // Filter by month and year
        if (isset($filters['month']) && isset($filters['year'])) {
            $query->whereMonth('created_at', $filters['month'])
                ->whereYear('created_at', $filters['year']);
        }

        // Filter by year only
        if (isset($filters['year']) && !isset($filters['month'])) {
            $query->whereYear('created_at', $filters['year']);
        }

        // Include trashed records
        if (isset($filters['include_trashed']) && $filters['include_trashed']) {
            $query->withTrashed();
        }

        // Only trashed records
        if (isset($filters['only_trashed']) && $filters['only_trashed']) {
            $query->onlyTrashed();
        }

        // Filter by user/author
        if (isset($filters['user_id']) && $filters['user_id'] !== '') {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by category
        if (isset($filters['category_id']) && $filters['category_id'] !== '') {
            $query->where('category_id', $filters['category_id']);
        }

        // Generic where filters
        if (isset($filters['where']) && is_array($filters['where'])) {
            foreach ($filters['where'] as $field => $value) {
                if ($value !== '' && $value !== null) {
                    $query->where($field, $value);
                }
            }
        }

        // Generic whereIn filters
        if (isset($filters['whereIn']) && is_array($filters['whereIn'])) {
            foreach ($filters['whereIn'] as $field => $values) {
                if (is_array($values) && !empty($values)) {
                    $query->whereIn($field, $values);
                }
            }
        }

        // Filters by published date
        if (!empty($filters['published_at'])) {
            $query->whereDate('published_at', '=', $filters['published_at']);
        }

        return $query;
    }

    /**
     * Apply sorting to query
     *
     * @param Builder $query
     * @param array $filters
     * @param string $defaultSort
     * @param string $defaultDirection
     * @return Builder
     */
    public static function applySorting(
        Builder $query,
        array $filters,
        string $defaultSort = 'created_at',
        string $defaultDirection = 'desc'
    ): Builder {
        $sortBy = $filters['sort_by'] ?? $defaultSort;
        $sortDirection = $filters['sort_direction'] ?? $defaultDirection;

        // Validasi sort direction
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
            ? strtolower($sortDirection)
            : $defaultDirection;

        $query->orderBy($sortBy, $sortDirection);

        return $query;
    }

    /**
     * Get filter summary for debugging or logging
     */
    public static function getFilterSummary(array $filters): array
    {
        $summary = [];

        if (isset($filters['search'])) {
            $summary['search'] = $filters['search'];
        }

        if (isset($filters['published'])) {
            $published = $filters['published'];

            if (is_string($published)) {
                $published = filter_var($published, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
        }

        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $summary['date_range'] = [
                'from' => $filters['date_from'] ?? null,
                'to' => $filters['date_to'] ?? null
            ];
        }

        if (isset($filters['include_trashed']) && $filters['include_trashed']) {
            $summary['includes_deleted'] = true;
        }

        return $summary;
    }

    /**
     * Parse date input fleksibel (YYYY, YYYY-MM, YYYY-MM-DD)
     */
    protected static function parseDateInput(string $date, bool $isEnd = false): ?string
    {
        if (preg_match('/^\d{4}$/', $date)) {
            return Carbon::createFromFormat('Y', $date)
                ->{$isEnd ? 'endOfYear' : 'startOfYear'}();
        }

        if (preg_match('/^\d{4}-\d{2}$/', $date)) {
            return Carbon::createFromFormat('Y-m', $date)
                ->{$isEnd ? 'endOfMonth' : 'startOfMonth'}();
        }

        return Carbon::parse($date)
            ->{$isEnd ? 'endOfDay' : 'startOfDay'}();
    }

    /**
     * Get monthly and yearly revenue statistics
     *
     * @param Builder $query
     * @param string $amountColumn
     * @param string $dateColumn
     * @return array
     */
    public static function getMonthlyRevenue(Builder $query, string $amountColumn = 'amount_received', string $dateColumn = 'created_at'): array
    {
        return $query->selectRaw("YEAR($dateColumn) as year, MONTH($dateColumn) as month, SUM($amountColumn) as total")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    public static function getYearlyRevenue(Builder $query, string $amountColumn = 'amount_received', string $dateColumn = 'created_at'): array
    {
        return $query->selectRaw("YEAR($dateColumn) as year, SUM($amountColumn) as total")
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }
}

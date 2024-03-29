<?php

namespace Aedart\Filters\Query\Filters;

use Aedart\Database\Query\Filter;
use Aedart\Filters\Query\Filters\Concerns\SortingCallbacks;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;

/**
 * Sort Query Filter
 *
 * Applies sorting to query (order by clauses).
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Filters\Query\Filters
 */
class SortFilter extends Filter
{
    use SortingCallbacks;

    /**
     * List of columns and their sorting direction
     *
     * @var array key-value pairs, key = column, value = sorting direction
     */
    protected array $columns = [];

    /**
     * @param array $columns key-value pairs, key = column, value = sorting direction (asc|desc)
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder|EloquentBuilder $query): Builder|EloquentBuilder
    {
        $callbacks = $this->getSortingCallbacks();

        // Add "order by ..." clause or use custom sorting callback when available.
        foreach ($this->columns as $column => $direction) {
            if (isset($callbacks[$column])) {
                $callback = $callbacks[$column];

                $query = $callback($query, $column, $direction);
                continue;
            }

            $query = $query->orderBy($column, $direction);
        }

        return $query;
    }
}

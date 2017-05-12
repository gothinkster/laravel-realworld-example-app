<?php

namespace App\RealWorld\Filters;

trait Filterable
{
    /**
     * Scope a query to apply given filter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Filter $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, Filter $filter)
    {
        return $filter->apply($query);
    }
}
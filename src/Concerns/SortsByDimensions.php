<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait SortsByDimensions
{
    protected $dimensionsSortModelTableName;

    public function sortDimensions(Builder $query, string $direction): void
    {
        $query->orderByRaw(
            "concat(
                {$this->lpadColumn('length')},
                'x',
                {$this->lpadColumn('width')},
                'x',
                {$this->lpadColumn('height')}
            ) {$direction}"
        );
    }

    public function scopeSortByDimensions(Builder $query, string $direction): void
    {
        $this->sortDimensions($query, $direction);
    }

    /**
     * Add a leading zero to any values that are less than 10
     * (have a string length less than 2 characters).
     *
     * This allows the computed `dimensions` column to sort correctly.
     */
    protected function lpadColumn(string $column): string
    {
        $table = $this->dimensionsSortModelTableName;
        if (! $table && $this instanceof Model) {
            $table = $this->getTable();
        }

        return "lpad({$table}.{$column}, greatest(length({$table}.{$column}), 2), '0')";
    }
}

<?php
namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

interface IFilter {
    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public function applyOr(Builder $builder, $value);

    public function applyAnd(Builder $builder, $value);
}
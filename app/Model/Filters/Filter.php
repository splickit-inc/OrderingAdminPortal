<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

abstract class Filter implements IFilter {

    public function applyAnd(Builder $builder, $value) {
        return $builder;
    }

    public function applyOr(Builder $builder, $value) {
        return $builder;
    }
}
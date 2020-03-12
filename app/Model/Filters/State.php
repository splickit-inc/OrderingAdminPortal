<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class State {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('state', 'LIKE', '%' . $value . '%');
    }
}
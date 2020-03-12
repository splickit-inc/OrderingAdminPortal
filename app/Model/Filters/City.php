<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class City {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('city', 'LIKE', '%' . $value . '%');
    }
}
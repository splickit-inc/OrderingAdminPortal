<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class Name {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('name', 'LIKE', '%' . $value . '%');
    }
}
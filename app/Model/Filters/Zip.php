<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class Zip {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('zip', 'LIKE', '%' . $value . '%');
    }
}
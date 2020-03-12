<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class BrandName extends Filter {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('brand_name', 'LIKE', '%' . $value . '%');
    }
}
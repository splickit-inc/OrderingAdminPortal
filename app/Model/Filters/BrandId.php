<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class BrandId extends Filter {

    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('brand_id', 'LIKE', '%' . $value . '%');
    }
}
<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class DisplayName {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('display_name', 'LIKE', '%' . $value . '%');
    }
}
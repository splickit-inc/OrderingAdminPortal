<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class ModelByFirstLetter extends Filter {

    public function applyAnd(Builder $builder, $value) {
        if (is_array($value) && array_has($value, 'column_name') && array_has($value, 'value')) {
            return $builder->where($value['column_name'], 'LIKE', $value['value'].'%');
        } else {
            return $builder;
        }
    }
}
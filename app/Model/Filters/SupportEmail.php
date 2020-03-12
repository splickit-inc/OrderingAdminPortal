<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class SupportEmail extends Filter {

    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('support_email', 'LIKE', '%' . $value . '%');
    }
}
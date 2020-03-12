<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class Location
{
    public function applyOr(Builder $builder, $value)
    {
        return $builder->orWhere('location', 'LIKE', '%' . $value . '%');
    }
}
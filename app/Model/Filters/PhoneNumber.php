<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class PhoneNumber
{
    public function applyOr(Builder $builder, $value)
    {
        return $builder->orWhere('phone_number', 'LIKE', '%' . $value . '%');
    }
}
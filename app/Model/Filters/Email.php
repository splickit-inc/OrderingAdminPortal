<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class Email
{
    public function applyOr(Builder $builder, $value)
    {
        return $builder->orWhere('email', 'LIKE', '%' . $value . '%');
    }
}
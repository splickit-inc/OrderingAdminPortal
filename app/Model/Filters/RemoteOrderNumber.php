<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class RemoteOrderNumber
{
    public function applyOr(Builder $builder, $value)
    {
        return $builder->orWhere('remote_order_number', 'LIKE', '%' . $value . '%');
    }
}
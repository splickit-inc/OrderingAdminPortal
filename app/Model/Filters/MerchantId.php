<?php

namespace App\Model\Filters;

use Illuminate\Database\Eloquent\Builder;

class MerchantId extends Filter {
    public function applyOr(Builder $builder, $value) {
        $table = $builder->getModel()->getTable();
        return $builder->orWhere($table.'.merchant_id', 'LIKE', '%' . $value . '%');
    }
}
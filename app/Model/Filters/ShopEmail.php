<?php

namespace App\Model\Filters;


use Illuminate\Database\Eloquent\Builder;

class ShopEmail {
    public function applyOr(Builder $builder, $value) {
        return $builder->orWhere('shop_email', 'LIKE', '%' . $value . '%');
    }
}
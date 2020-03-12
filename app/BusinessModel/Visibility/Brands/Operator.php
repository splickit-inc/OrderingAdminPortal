<?php

namespace App\BusinessModel\Visibility\Brands;

use Illuminate\Support\Facades\Auth;

class Operator extends Visibility {

    function getAllRecords() {
        return $this->model
            ->join('Merchant', 'Brand2.brand_id', '=', 'Merchant.brand_id')
            ->join('portal_operator_merchant_map', 'Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
            ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
            ->orderBy('Brand2.brand_name')
            ->distinct();
    }
}
<?php

namespace App\BusinessModel\Visibility\Merchant;

use Illuminate\Support\Facades\Auth;

class Operator extends MerchantVisibility {

    function getMerchants() {
        return $this->model
            ->join('portal_operator_merchant_map', function ($join) {
                $join->on('Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                    ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id);
            })
            ->where('Merchant.active', '=', 'Y')
            ->where('Merchant.logical_delete', '=', 'N')
            ->select('Merchant.*')
            ->distinct();
    }

    function getMerchantsByMenu() {
        $current_menu = session('current_menu_id');
        return $this->model
            ->join('portal_operator_merchant_map', function ($join) {
                $join->on('Merchant.merchant_id', '=', 'portal_operator_merchant_map.merchant_id')
                    ->where('portal_operator_merchant_map.user_id', '=', Auth::user()->id)
                    ->where('logical_delete', '=', 'N');
            })
            ->join('Merchant_Menu_Map', function ($join) use ($current_menu) {
                $join->on('Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
                    ->where('Merchant_Menu_Map.menu_id', '=', $current_menu);
            })
            ->select('Merchant.*')
            ->distinct();
    }
}
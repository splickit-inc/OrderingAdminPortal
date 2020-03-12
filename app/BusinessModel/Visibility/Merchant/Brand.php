<?php

namespace App\BusinessModel\Visibility\Merchant;

use Illuminate\Support\Facades\Auth;

class Brand extends MerchantVisibility {
    public function getMerchants() {

        return $this->model
            ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
            ->join('portal_brand_manager_brand_map', function ($join) {
                $join->on('Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                    ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id);
            })
            ->where('Merchant.active', '=', 'Y')
            ->where('Merchant.logical_delete', '=', 'N')
            ->select('Merchant.*')
            ->distinct();
    }

    public function getMerchantsByMenu() {
        $current_menu = session('current_menu_id');

        return $this->model
            ->join('Brand2', 'Merchant.brand_id', '=', 'Brand2.brand_id')
            ->join('portal_brand_manager_brand_map', function ($join) {
                $join->on('Merchant.brand_id', '=', 'portal_brand_manager_brand_map.brand_id')
                    ->where('portal_brand_manager_brand_map.user_id', '=', Auth::user()->id);
            })
            ->join('Merchant_Menu_Map', function ($join) use ($current_menu) {
                $join->on('Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
                    ->where('Merchant_Menu_Map.menu_id', '=', $current_menu);
            })
            ->where('Merchant.logical_delete', '=', 'N')
            ->select('Merchant.*')
            ->distinct();
    }
}
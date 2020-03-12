<?php

namespace App\BusinessModel\Visibility\Merchant;


class MGlobal extends MerchantVisibility {
    public function getMerchants() {
        return $this->model->newQuery();
    }

    public function getMerchantsByMenu() {
        return $this->model
            ->join('Merchant_Menu_Map', function ($join) {
                $join->on('Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
                    ->where('Merchant_Menu_Map.menu_id', '=', session('current_menu_id'));
            })
            ->select('Merchant.*')
            ->distinct();
    }
}
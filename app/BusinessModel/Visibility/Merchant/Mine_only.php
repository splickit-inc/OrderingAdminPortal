<?php

namespace App\BusinessModel\Visibility\Merchant;

class Mine_only extends MerchantVisibility {

    public function getMerchants() {
        return $this->model
            ->join('portal_object_ownership', function ($join) {
                $user_owner_ids = implode(', ', session('user_child_users'));
                $join->on('Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
                    ->where('portal_object_ownership.object_type', '=', 'merchant')
                    ->whereIn('portal_object_ownership.user_id', $user_owner_ids);
            })
            ->where('Merchant.logical_delete', '=', 'N')
            ->select('Merchant.*')
            ->distinct();
    }

    public function getMerchantsByMenu() {
        $current_menu = session('current_menu_id');

        return $this->model
            ->join('portal_object_ownership', function ($join) {
                $user_owner_ids = implode(', ', session('user_child_users'));
                $join->on('Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
                    ->where('portal_object_ownership.object_type', '=', 'merchant')
                    ->whereIn('portal_object_ownership.user_id', $user_owner_ids)
                    ->where('logical_delete', '=', 'N');
            })->join('Merchant_Menu_Map', function ($join) use ($current_menu) {
                $join->on('Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
                    ->where('Merchant_Menu_Map.menu_id', '=', $current_menu);
            })
            ->where('Merchant.active', '=', 'Y')
            ->where('Merchant.logical_delete', '=', 'N')
            ->select('Merchant.*')
            ->distinct();
    }
}
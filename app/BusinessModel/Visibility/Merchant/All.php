<?php

namespace App\BusinessModel\Visibility\Merchant;

class All extends MerchantVisibility {
    public function getMerchants() {
        return $this->model
            ->join('portal_object_ownership', function ($join) {
                $join->on('Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
                    ->where('portal_object_ownership.object_type', '=', 'merchant')
                    ->where('portal_object_ownership.organization_id', '=', session('user_organization_id'));
            })
            ->where('Merchant.active', '=', 'Y')
            ->where('Merchant.logical_delete', '=', 'N');
    }

    public function getMerchantsByMenu() {
        $current_menu = session('current_menu_id');

        return $this->model
            ->join('portal_object_ownership', function ($join) {
                $join->on('Merchant.merchant_id', '=', 'portal_object_ownership.object_id')
                    ->where('portal_object_ownership.object_type', '=', 'merchant')
                    ->where('portal_object_ownership.organization_id', '=', session('user_organization_id'));
            })
            ->join('Merchant_Menu_Map', function ($join) use ($current_menu) {
                $join->on('Merchant_Menu_Map.merchant_id', '=', 'Merchant.merchant_id')
                    ->where('Merchant_Menu_Map.menu_id', '=', $current_menu);
            })
            ->where('Merchant.active', '=', 'Y')
            ->select('Merchant.*')
            ->distinct();
    }
}
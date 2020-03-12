<?php

namespace App\BusinessModel\Visibility\Brands;


class Mine_only extends Visibility {

    function getAllRecords() {
        $user_owner_ids = implode(', ', session('user_child_users'));

        return $this->model
            ->join('portal_object_ownership', 'Brand2.brand_id', '=', 'portal_object_ownership.object_id')
            ->where('portal_object_ownership.object_type', '=', 'brand')
            ->where('portal_object_ownership.user_id', '=', $user_owner_ids)
            ->where('Brand2.active', '=', 'Y')
            ->distinct();
    }
}
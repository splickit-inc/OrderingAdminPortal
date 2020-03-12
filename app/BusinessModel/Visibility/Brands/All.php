<?php

namespace App\BusinessModel\Visibility\Brands;


class All extends Visibility {

    function getAllRecords() {
        return $this->model
            ->join('portal_object_ownership', 'Brand2.brand_id', '=', 'portal_object_ownership.object_id')
            ->where('portal_object_ownership.object_type', '=', 'brand')
            ->where('portal_object_ownership.organization_id', '=', session('user_organization_id'))
            ->where('Brand2.active', '=', 'Y')
            ->orderBy('Brand2.brand_name')
            ->distinct();
    }
}
<?php

namespace App\BusinessModel\Visibility\Brands;

class VGlobal extends Visibility {

    function getAllRecords() {
        return $this->model->where('production', '=', 'Y')->where('active', '=', 'Y')->orderBy('Brand2.brand_name');
    }
}
<?php

namespace App\BusinessModel\Visibility\ParkingLot;



class Brand extends Visibility
{

    static function getName()
    {
        return 'brand';
    }

    function getParkingLotRecords($order_by = 'phone_number', $order_direction = 'ASC')
    {
        $brand_id = session('brand_manager_brand');
        return $this->model->newQuery()->where('brand_id', '=', $brand_id);
    }
}
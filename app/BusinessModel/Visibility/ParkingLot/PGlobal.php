<?php

namespace App\BusinessModel\Visibility\ParkingLot;



class PGlobal extends Visibility
{

    static function getName()
    {
        return 'global';
    }

    /**
     * @inheritdoc
     */
    function getParkingLotRecords($order_by = 'phone_number', $order_direction = 'ASC')
    {
        return $this->model->newQuery();
    }
}
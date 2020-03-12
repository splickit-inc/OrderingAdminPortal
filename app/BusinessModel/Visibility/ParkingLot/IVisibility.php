<?php

namespace App\BusinessModel\Visibility\ParkingLot;


use Illuminate\Database\Eloquent\Builder;

interface IVisibility
{
    /**
     * @param string $order_by
     * @param string $order_direction
     * @return Builder
     */
    function getParkingLotRecords($order_by = 'phone_number', $order_direction = 'ASC');

    /**
     * @param string $search_text
     * @param string $order_by
     * @param string $order_direction
     * @return Builder
     */
    function searchParkingLotRecords($search_text, $order_by = 'phone_number', $order_direction = 'ASC');

    /**
     * @return string
     */
    static function getName();
}
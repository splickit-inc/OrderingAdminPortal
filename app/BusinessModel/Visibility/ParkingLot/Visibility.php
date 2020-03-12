<?php

namespace App\BusinessModel\Visibility\ParkingLot;


use App\Model\Filters\FilterApplier;
use App\Model\LoyaltyParkingLot;

abstract class Visibility implements IVisibility
{
    /** @var LoyaltyParkingLot $model */
    protected $model;

    /**
     * @var FilterApplier
     */
    protected $filterApplier;

    public function __construct(LoyaltyParkingLot $model, FilterApplier $filterApplier)
    {
        $this->model = $model;
        $this->filterApplier = $filterApplier;
    }

    function searchParkingLotRecords($search_text, $order_by = 'phone_number', $order_direction = 'ASC')
    {
        return $this->filterApplier->searchWithValue([
            'phone_number',
            'email',
            'remote_order_number',
            'location'
        ], $search_text, $this->getParkingLotRecords())->orderBy($order_by, $order_direction);
    }
}
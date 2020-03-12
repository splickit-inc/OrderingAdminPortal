<?php

namespace App\BusinessModel\Orders\FilterType;


use Illuminate\Database\Query\Builder;

class MultipleMerchant
{
    protected $merchants;

    public function __construct($merchants)
    {
        $this->merchants = $merchants;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    function addFilterTypeToQuery($query)
    {
        return $query->whereIn('merchant_id', $this->merchants);
    }
}